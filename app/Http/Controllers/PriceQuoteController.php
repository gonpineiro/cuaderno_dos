<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Requests\Order\StoreOnlineOrderRequest;
use App\Http\Requests\Order\StoreSiniestroOrderRequest;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\PriceQuote\StorePriceQuoteRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PriceQuote\PriceQuoteProductResource;
use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Models\Order;
use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
use App\Models\Table;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PriceQuoteController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        if ($request->type === 'pendiente') {
            $priceQuote = PriceQuote::with('order')->orderByDesc('created_at')->get();
        } else if ($request->type === 'pedido') {
            $priceQuote = PriceQuote::with('order')
                ->whereHas('order', function ($query) {
                    $query->where('type_id', 6);
                })
                ->whereNotNull('order_id')
                ->orderByDesc('created_at')
                ->get();
        } else if ($request->type === 'siniestro') {
            $priceQuote = PriceQuote::with('order')
                ->whereHas('order', function ($query) {
                    $query->where('type_id', 8);
                })
                ->whereNotNull('order_id')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $priceQuote = PriceQuote::orderByDesc('created_at')->get();
        }

        return sendResponse(PriceQuoteResource::collection($priceQuote));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PriceQuote\StorePriceQuoteRequest $request
     * @return \App\Http\Resources\PriceQuote\PriceQuoteResource|\Illuminate\Http\JsonResponse
     */
    public function store(StorePriceQuoteRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $data = $request->all();
            $data['user_id'] = $user->id;

            $price_quote = PriceQuote::create($data);

            /* Intentamos guardar lss price_quotenes productos */
            if (!$this->storePriceQuoteProduct($request, $price_quote->id)) {
                DB::rollBack();
                return sendResponse(null, 'No se pudieron guardar los productos de la orden');
            }

            DB::commit();

            return sendResponse(new PriceQuoteResource($price_quote, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    private function storePriceQuoteProduct($request, $price_quote_id)
    {
        $detail = $request->detail;

        /* if ($this->hayDuplicados($detail)) {
            throw new Exception("Existen productos duplicados");
        } */

        foreach ($detail as $item) {
            $item['price_quote_id'] = $price_quote_id;
            $item['state_id'] = $item['state']['id'];

            if ($item['product']['is_product']) {
                $item['product_id'] = $item['product']['id'];
            } else {
                $item['other_id'] = $item['product']['id'];
            }

            if (!PriceQuoteProduct::create($item)) {
                throw new \Exception("No se pudo crear un detalle de la cotización");
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order $priceQuote
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $priceQuote = PriceQuote::findOrFail($id);
        return sendResponse(new PriceQuoteResource($priceQuote, 'complete'));
    }

    public function asignarSiniestro(StoreSiniestroOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $priceQuote = PriceQuote::find($request->price_quote_id);

            if (!$priceQuote) {
                throw new \Exception('No existe la cotizacion');
            }

            if ($priceQuote->order_id) {
                throw new \Exception('La cotización ya tiene un pedido/siniestro asignado');
            }

            /** El typo_pedido que tendra el pedido */
            $type_order = Table::find($request->type_id);

            if ($type_order->name !== 'order_type' && $type_order->value !== 'siniestro') {
                throw new \Exception('Enviando información erronea al servidor');
            }

            $order = OrderController::saveSiniestroOrder($request);

            $priceQuote->order_id = $order->id;
            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function asignarOnline(StoreOnlineOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $priceQuote = PriceQuote::find($request->price_quote_id);

            if (!$priceQuote) {
                throw new \Exception('No existe la cotizacion');
            }

            if ($priceQuote->order_id) {
                throw new \Exception('La cotización ya tiene un pedido/siniestro asignado');
            }

            /** El typo_pedido que tendra el pedido */
            $type_order = Table::find($request->type_id);

            if ($type_order->name !== 'order_type' && $type_order->value !== 'online') {
                throw new \Exception('Enviando información erronea al servidor');
            }

            $order = OrderController::saveOnlineOrder($request);

            $priceQuote->order_id = $order->id;
            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function asignarCliente(StoreClienteOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $priceQuote = PriceQuote::find($request->price_quote_id);

            if (!$priceQuote) {
                throw new \Exception('No existe la cotizacion');
            }

            if ($priceQuote->order_id) {
                throw new \Exception('La cotización ya tiene un pedido/siniestro asignado');
            }

            /** El typo_pedido que tendra el pedido */
            $type_order = Table::find($request->type_id);

            if ($type_order->name !== 'order_type' && $type_order->value !== 'cliente') {
                throw new \Exception('Enviando información erronea al servidor');
            }

            $order = OrderController::saveClienteOrder($request);

            $priceQuote->order_id = $order->id;
            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $priceQuote = PriceQuote::findOrFail($id);

            if ($priceQuote->order_id) {
                throw new \Exception('No se puede borrar una cotización asignada');
            }

            $priceQuote->delete();
            PriceQuoteProduct::where('price_quote_id', $id)->delete();

            DB::commit();

            return sendResponse($id);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $id);
        }
    }

    public function update_price_quote_product(Request $request)
    {
        $item =
            PriceQuoteProduct::where('price_quote_id', $request->price_quote_id)
            ->where('product_id', $request->product_id)->first();

        $update = $item->update($request->all());

        if ($update) {
            $priceQuote = PriceQuote::findOrFail($request->price_quote_id);
            return sendResponse(new PriceQuoteResource($priceQuote, 'complete'));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }

    public function getPdf(Request $request, $id)
    {
        $order = PriceQuote::find($id);
        $order->client;
        $detail = PriceQuoteProductResource::collection($order->detail);

        if ($request->type == 'total' || $request->type == 'interno') {
            $total = 0;
            foreach ($detail as $item) {
                $total += $item['amount'] * $item['unit_price'];
            }
        }

        if ($request->type == 'sin_total') {
            $total = null;
        }

        $vars = [
            'cotizacion' => $order,
            'detail' => $detail,
            'total' => $total,
            'type' => $request->type
        ];

        $pdf = Pdf::loadView('pdf.cotizaciones.detalle', $vars);

        return $pdf->download('informe.pdf');
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $order = PriceQuote::findOrFail($id);

            $order->fill($data)->save();

            /* CORREGIR; NO HAY QUE BORRAR; HAY QUE ACTUALIZAR */
            PriceQuoteProduct::where('price_quote_id', $id)->delete();

            /* Intentamos guardar lss ordernes productos */
            if (!$this->storePriceQuoteProduct($request, $order->id)) {
                DB::rollBack();

                return response()->json([
                    'data' => null,
                    'message' => null,
                    'error' => 'No se pudieron guardar los productos de la cotizacion'
                ]);
            }

            DB::commit();

            return sendResponse(new PriceQuoteResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
