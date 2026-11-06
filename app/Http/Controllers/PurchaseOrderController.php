<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\PedirResource;
use App\Http\Resources\PurchaseOrder\ProductEvaluarPedirResource;
use App\Http\Resources\PurchaseOrder\PurchaseOrderResource;
use App\Http\TraitsControllers\TraitPedidosEmail;
use App\Models\Product;
use App\Models\ProductJazz;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\Table;
use App\Models\ToAsk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with(['detail', 'provider'])->orderByDesc('created_at')->get();

        return sendResponse(PurchaseOrderResource::collection($purchaseOrder));
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        return sendResponse(new PurchaseOrderResource($purchaseOrder, 'complete'));
    }

    public function generar_pedir(Request $request)
    {
        $detail = $request->all();
        return ToAsk::insert($detail);
    }

    public function producto_generar_pedir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'provider_id' => 'required|integer|exists:providers,id',
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return sendResponse(null, $validator->errors(), 422);
        }

        $body = $request->only(['product_id', 'provider_id', 'amount']);
        $body['user_id'] = auth()->user()->id;

        $toAsk = ToAsk::create($body);

        return new PedirResource($toAsk);
    }

    public function producto_modificar_pedir(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:to_ask,id',
                'provider_id' => 'required|integer|exists:providers,id',
                'amount' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return sendResponse(null, $validator->errors(), 422);
            }

            DB::beginTransaction();

            $to_ask = ToAsk::where('purchase_order', true)->findOrFail($request->id);

            $to_ask->provider_id = $request->provider_id;
            $to_ask->amount = $request->amount;

            $to_ask->save();

            DB::commit();
            return $this->pedir();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage());
        }
    }

    public function pedir()
    {
        /* $orderProducts = OrderProduct::whereHas('product', function ($query) {
            $query->where('is_special', true);
        })->orderBy('order_id', 'asc')->get(); */

        $to_ask = ToAsk::where('purchase_order', true)->get();

        return sendResponse(PedirResource::collection($to_ask));
    }

    public function evaluar_pedir()
    {
        $products = Product::whereNotNull('idProducto')
            ->whereHas('jazz', function ($query) {
                $query->whereColumn('stock', '<=', 'stock_min')
                    ->where('punto_pedido', '>', 0);
            })
            ->with('jazz', 'provider', 'toAsk')
            ->withSum('toAsk as total_to_ask', 'amount')
            ->get();

        return sendResponse(ProductEvaluarPedirResource::collection($products));
        //return sendResponse($products);
    }

    public function listado_producto_generar_pedir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array|min:1',
            'data.*.product_id' => 'required|integer|exists:products,id',
            'data.*.provider_id' => 'required|integer|exists:providers,id',
            'data.*.amount' => 'required|numeric|min:0',
            'data.*.stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return sendResponse(null, $validator->errors(), 422);
        }

        $data = $request->data;

        try {
            DB::transaction(function () use ($data) {
                foreach ($data as $d) {
                    $amount = $d['amount'] - $d['stock'];

                    if ($amount <= 0) {
                        throw new \Exception('La cantidad calculada debe ser mayor a cero');
                    }

                    ToAsk::create([
                        'product_id' => $d['product_id'],
                        'provider_id' => $d['provider_id'],
                        'amount' => $amount,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 422);
        }

        $count = count($data);
        return sendResponse("Se agregaron $count productos a pedir");
    }

    public function generar_orden(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider.id' => 'required|integer|exists:providers,id',
            'to_asks' => 'required|array|min:1',
            'to_asks.*' => 'required|integer|distinct|exists:to_ask,id',
        ]);

        if ($validator->fails()) {
            return sendResponse(null, $validator->errors(), 422);
        }

        $body = $request->all();

        DB::beginTransaction();

        try {

            $state = Table::where('name', 'purchase_order')->where('value', 'pendiente')->firstOrFail();

            $toAsks = ToAsk::whereIn('id', $body['to_asks'])
                ->where('provider_id', $body['provider']['id'])
                ->where('purchase_order', true)
                ->lockForUpdate()
                ->get();

            if ($toAsks->count() !== count($body['to_asks'])) {
                throw new \Exception('Uno o más productos pendientes ya no están disponibles');
            }

            if ($toAsks->contains(fn ($toAsk) => $toAsk->amount <= 0)) {
                throw new \Exception('No se puede generar una orden con cantidades no válidas');
            }

            $purchaseOrder = PurchaseOrder::create([
                'provider_id' => $body['provider']['id'],
                'state_id' => $state->id,
            ]);

            foreach ($toAsks->groupBy('product_id') as $productId => $items) {
                PurchaseOrderProduct::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'amount' => $items->sum('amount'),
                    'product_id' => $productId,
                ]);
            }

            foreach ($toAsks as $toAsk) {
                $toAsk->purchase_order = 0;
                $toAsk->save();
            }

            DB::commit();


            return $this->pedir();
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function update(Request $request, $id)
    {
        if (!PurchaseOrder::whereKey($id)->exists()) {
            return sendResponse(null, 'No existe la orden de compra', 404);
        }

        $validator = Validator::make($request->all(), [
            'state_id' => 'required|integer|exists:tables,id',
            'observation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return sendResponse(null, $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $previousStateId = $purchaseOrder->state_id;

            $entregado = Table::where('name', 'purchase_order')->where('value', 'entregado')->firstOrFail();

            if ($purchaseOrder->state_id == $entregado->id) {
                throw new \Exception("No se puede cambiar de estado");
            }

            $purchaseOrder->update($request->only(['state_id', 'observation']));

            $state = Table::where('id', $request->state_id)
                ->where('name', 'purchase_order')
                ->firstOrFail();

            if ($state->value == 'enviado' && $previousStateId != $state->id) {
                $purchaseOrder->refresh();
                TraitPedidosEmail::ordenCompra($purchaseOrder);
            }

            DB::commit();

            return sendResponse(new PurchaseOrderResource($purchaseOrder, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:to_ask,id',
        ]);

        if ($validator->fails()) {
            return sendResponse(null, $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $to_ask = ToAsk::where('purchase_order', true)->findOrFail($request->id);

            $to_ask->delete();

            DB::commit();

            return sendResponse($to_ask);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
