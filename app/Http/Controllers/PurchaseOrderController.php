<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\PedirResource;
use App\Http\Resources\PurchaseOrder\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\Table;
use App\Models\ToAsk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $toAsk =ToAsk::create($request->all());

        return new PedirResource($toAsk);
    }

    public function pedir()
    {
        /* $orderProducts = OrderProduct::whereHas('product', function ($query) {
            $query->where('is_special', true);
        })->orderBy('order_id', 'asc')->get(); */

        $to_ask = ToAsk::where('purchase_order', true)->get();

        return sendResponse(PedirResource::collection($to_ask));
    }

    public function generar_orden(Request $request): \Illuminate\Http\JsonResponse
    {
        $body = $request->all();

        DB::beginTransaction();

        try {

            $state = Table::where('name', 'purchase_order')->where('value', 'pendiente')->first();
            $purchaseOrder = PurchaseOrder::create([
                'provider_id' => $body['provider']['id'],
                'state_id' => $state->id,
            ]);

            foreach ($body['products'] as $product) {
                PurchaseOrderProduct::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $product['id'],
                    'amount' => $product['amount'],
                ]);
            }

            foreach ($body['to_asks'] as $to_ask_id) {
                $toAsk = ToAsk::find($to_ask_id);
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
        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::find($id);

            $entregado = Table::where('name', 'purchase_order')->where('value', 'entregado')->first();

            if ($purchaseOrder->state_id == $entregado->id) {
                throw new \Exception("No se puede cambiar de estado");
            }

            $purchaseOrder->update($request->all());

            DB::commit();

            return sendResponse(new PurchaseOrderResource($purchaseOrder, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
