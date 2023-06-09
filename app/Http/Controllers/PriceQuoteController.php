<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\PriceQuote\StorePriceQuoteRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Models\Order;
use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
use App\Models\Table;
use Illuminate\Http\Request;

class PriceQuoteController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $priceQuote = PriceQuoteResource::collection(PriceQuote::all());
        return sendResponse($priceQuote);
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
        $price_quotes_products = $request->orders_products;

        /* if ($this->hayDuplicados($price_quotes_products)) {
            throw new Exception("Existen productos duplicados");
        } */

        foreach ($price_quotes_products as $price_quote_product) {

            $price_quote_product['price_quote_id'] = $price_quote_id;
            $price_quote_product['state_id'] = $price_quote_product['state']['id'];

            if ($price_quote_product['product']['is_product']) {
                $price_quote_product['product_id'] = $price_quote_product['product']['id'];
            } else {
                $price_quote_product['other_id'] = $price_quote_product['product']['id'];
            }

            if (!PriceQuoteProduct::create($price_quote_product)) {
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

    public function asignar(Request $request)
    {
        DB::beginTransaction();

        try {
            $cotizacion = PriceQuote::find($request->price_quote_id);

            if (!$cotizacion) {
                throw new \Exception('No existe la cotizacion');
            }

            if ($cotizacion->order_id) {
                throw new \Exception('La cotización ya tiene un pedido asignado');
            }

            $orderRequest = StoreOrderRequest::createFrom($request);
            $orderRequest->validate($orderRequest->rules());
            $order = OrderController::saveOrder($orderRequest);

            $cotizacion->order_id = $order->id;
            $cotizacion->save();

            DB::commit();

            return sendResponse([
                'pedido' => new OrderResource($order),
                'cotizacion' => new PriceQuoteResource($cotizacion),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
