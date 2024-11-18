<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Requests\Order\StoreOnlineOrderRequest;
use App\Http\Requests\Order\StoreSiniestroOrderRequest;
use App\Http\Resources\Shipment\ShipmentResource;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\PriceQuote\StorePriceQuoteRequest;
use App\Http\Requests\PriceQuote\UpdatePriceQuoteRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PriceQuote\PriceQuoteProductResource;
use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Models\Client;
use App\Models\ClientChasis;
use App\Models\Coeficiente;
use App\Models\Order;
use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
use App\Models\Shipment;
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

    public function search(Request $request)
    {
        $query = PriceQuote::query();

        foreach ($request->all() as $key => $value) {
            if (!$value) {
                continue; // Ignorar valores vacíos o nulos
            }

            switch ($key) {
                case 'client':
                    $query->whereHas('client', function ($q) use ($value) {
                        $q->where('name', 'LIKE', '%' . $value . '%');
                    });
                    break;

                case 'vehiculo':
                    $query->whereHas('vehiculo', function ($q) use ($value) {
                        $q->where('name', 'LIKE', '%' . $value . '%');
                    });
                    break;
                case 'user':
                    $query->whereHas('user', function ($q) use ($value) {
                        $q->where('name', 'LIKE', '%' . $value . '%');
                    });
                    break;

                default:
                    $query->where($key, 'LIKE', '%' . $value . '%');
                    break;
            }
        }

        return sendResponse(PriceQuoteResource::collection($query->get()));
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
            unset($data['created_at']);

            $price_quote = PriceQuote::create($data);

            ClientChasis::updateElement($price_quote);

            /* Intentamos guardar lss price_quotenes productos */
            if (!$this->storePriceQuoteProduct($request, $price_quote->id)) {
                DB::rollBack();
                return sendResponse(null, 'No se pudieron guardar los productos de la orden');
            }

            /* Actualizamos los datos del cliente con informacion de la cotizacion */
            $client = Client::find($price_quote->client_id);
            if (!$client->is_company && !$client->is_insurance) {
                $client->vehiculo_id = $price_quote->vehiculo_id;
                $client->chasis = $price_quote->chasis;
                $client->year = $price_quote->year;
                $client->save();
            }

            DB::commit();

            return sendResponse(new PriceQuoteResource($price_quote));
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

            $item['provider_id'] = isset($item['provider']) ? $item['provider']['id'] : null;
            $item['product_id'] = $item['product']['id'];

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
    public function show(Request $requets, $id)
    {
        $priceQuote = PriceQuote::findOrFail($id);
        if ($requets->type) {
            $method = $requets->type;
            return sendResponse(PriceQuoteResource::$method($priceQuote));
        }
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

            $this->save_shipment($request, $order);

            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
                'order_products' => $order->detail
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

            $this->save_shipment($request, $order);

            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
                'order_products' => $order->detail
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

            $this->save_shipment($request, $order);

            $priceQuote->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order, 'complete'),
                'cotizacion' => new PriceQuoteResource($priceQuote),
                'order_products' => $order->detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    private function save_shipment(Request $request, $order)
    {
        if ($request->envio) {
            $shipment = ShipmentController::storeShipment($request->envio, $order);

            $order->payment_method_id = $request->envio['payment_method_id'];
            $order->shipment_id = $shipment->id;
            $order->save();

            $order->setShipmentState();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $priceQuote = PriceQuote::findOrFail($request->id);

            if ($priceQuote->order_id) {
                throw new \Exception('Existe un pedido generado desde esta cotización');
            }

            $priceQuote->delete();
            PriceQuoteProduct::where('price_quote_id', $request->id)->delete();

            DB::commit();

            return sendResponse(new PriceQuoteResource($priceQuote));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->id);
        }
    }

    public function update_price_quote_product(Request $request)
    {

        $price_quote = PriceQuote::findOrFail($request->price_quote_id);

        if ($price_quote->order) {
            return sendResponse(null, 'Existe un pedido generado desde esta cotización');
        }

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

        $is_contado = $order->type_price->value == 'contado';

        $contado_deb = $is_contado ? Coeficiente::find(2) : null;

        $truncate = $request->type === 'interno' ? 44 : 59;
        $detail = PriceQuoteProductResource::pdfArray($order->detail_cotizable, $contado_deb,  $truncate);
        //$detail_lista = PriceQuoteProductResource::pdfArray($order->detail_cotizable, null,  $truncate);

        $total = get_total_price($detail);

        $vars = [
            'cotizacion' => $order,
            'detail' => PriceQuoteProductResource::formatPdf($detail),
            'coefs' => $this->get_total_calculadora($order->detail_cotizable, $contado_deb),
            'total' => formatoMoneda($total),
            'type' => $request->type,
            'is_contado' => $is_contado
        ];

        $pdf = Pdf::loadView("pdf.cotizaciones.$request->type", $vars);

        return $pdf->download('informe.pdf');
    }

    private function get_total_calculadora($detail_lista)
    {
        $contado_deb = Coeficiente::find(2);
        $coefs = Coeficiente::where('show', true)->orderBy('position', 'asc')->get()->toArray();

        $calculadora =  array_map(function ($coef) use ($detail_lista, $contado_deb) {
            $multiplo = $coef['coeficiente'] * $coef['value'];
            $total = 0;
            foreach ($detail_lista as $value) {
                $valor = !$coef['cuotas'] ?
                    redondearNumero($value['unit_price'] * $multiplo) :
                    redondearNumero($value['unit_price'] * $contado_deb->coeficiente) * $coef['value'];

                //$valor = redondearNumero($value['unit_price'] * $multiplo);
                $total += $valor * $value['amount'];
            }

            return [
                'description' => $coef['description'],
                'price' => formatoMoneda($total),
                //'price' => formatoMoneda(redondearNumero($total)),
                /* 'valor_cuota' => $coef['cuotas'] ? formatoMoneda($total / $coef['cuotas'], 2) : ' ' */
                'valor_cuota' => $coef['cuotas'] ? formatoMoneda($total / $coef['cuotas']) : ' '
            ];
        }, $coefs);
        return $calculadora;
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $price_quote = PriceQuote::findOrFail($id);

            if ($price_quote->order) {
                return sendResponse(null, 'Existe un pedido generado desde esta cotización');
            }

            $detail = $request->detail;

            // Obtén los IDs de producto de detail
            $productIdsInDetail = array_map(function ($item) {
                return $item['product']['id'];
            }, $detail);

            // Elimina los registros OrderProduct que no están en $productIdsInDetail
            PriceQuoteProduct::where('price_quote_id', $id)
                ->whereNotIn('product_id', $productIdsInDetail)
                ->delete();

            // Actualiza o agrega registros OrderProduct según detail
            foreach ($detail as $item) {
                $price_quoteProductData = [
                    'price_quote_id' => $price_quote->id,
                    'product_id' => $item['product']['id'],
                    'amount' => $item['amount'],
                    'unit_price' => $item['unit_price'],
                    /* 'description' => $item['description'], */
                    'state_id' => $item['state']['id'],
                    'provider_id' => isset($item['provider']) ? $item['provider']['id'] : null,
                ];

                $priceQuoteProduct = PriceQuoteProduct::where('price_quote_id', $price_quote->id)
                    ->where('product_id', $item['product']['id'])
                    ->first();

                if ($priceQuoteProduct) {
                    $priceQuoteProduct->update($price_quoteProductData);
                } else {
                    PriceQuoteProduct::create(array_merge([
                        'price_quote_id' => $price_quote->id,
                        'product_id' => $item['product']['id'],
                    ], $price_quoteProductData));
                }
            }

            DB::commit();
            return sendResponse(new PriceQuoteResource($price_quote, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function updateCotizacion(UpdatePriceQuoteRequest $request, int $id)
    {
        $price_quote = PriceQuote::findOrFail($id);

        if ($price_quote->order) {
            return sendResponse(null, 'Existe un pedido generado desde esta cotización');
        }

        $price_quote->update($request->all());

        return sendResponse(new PriceQuoteResource($price_quote, 'complete'));
    }
}
