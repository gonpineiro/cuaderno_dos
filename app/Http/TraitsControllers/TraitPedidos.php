<?php

namespace App\Http\TraitsControllers;

use App\Http\DB\DBPedidosTrait;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\TraitsControllers\TraitPedidosEmail;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\PedidoOnline;
use App\Models\Siniestro;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

trait TraitPedidos
{
    use DBPedidosTrait;

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = $this->getPedidosQuery();

            if ($request->filled('last_id')) {
                $query->where('orders.id', '>', (int) $request->last_id);
            }

            $pedidos = $query->limit(500)->get();
            $collection = OrderResource::collection($pedidos);
            return sendResponse($collection);
        } catch (\Throwable $th) {
            return sendResponse($th->getMessage());
        }
    }

    private function getPedidos()
    {
        $siniestro = Table::where('name', 'order_type')->where('value', 'siniestro')->first();

        $query = Order::where('type_id', '!=', $siniestro->id)->orderByDesc('created_at');

        $pedidos = $query->take(300)->get();

        return $pedidos;
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (empty(array_filter($request->all()))) {
                return $this->index($request);
            }

            $query = $this->getPedidosQuery();

            foreach ($request->all() as $key => $value) {
                if (!$value) {
                    continue;
                }

                switch ($key) {
                    case 'client_name':
                        $query->whereHas('client', function ($q) use ($value) {
                            $q->where('name', 'LIKE', "%{$value}%");
                        });
                        break;

                    case 'client_phone':
                        $query->whereHas('client', function ($q) use ($value) {
                            $q->where('phone', 'LIKE', "%{$value}%");
                        });
                        break;

                    case 'vehiculo':
                        $query->whereHas('vehiculo', function ($q) use ($value) {
                            $q->where('name', 'LIKE', "%{$value}%");
                        });
                        break;

                    case 'payment_method':
                        $query->whereHas('payment_method', function ($q) use ($value) {
                            $q->where('description', 'LIKE', "%{$value}%");
                        });
                        break;

                    case 'estimated_date':
                        applyDateFilter($query, 'orders.estimated_date', $value);
                        break;

                    case 'created_at':
                        applyDateFilter($query, 'orders.created_at', $value);
                        break;

                    default:
                        $query->where("orders.$key", 'LIKE', "%{$value}%");
                        break;
                }
            }

            $pedidos = $query->limit(5000)->get();

            return sendResponse(OrderResource::collection($pedidos));
        } catch (\Throwable $th) {
            return sendResponse(null, $th->getMessage(), 500);
        }
    }


    private function ordenarPedidos($pedidos)
    {
        $pedidos = $pedidos->sortBy(function ($order) {
            return [
                'incompleto' => 1,
                'pendiente' => 2,
                'retirar' => 3,
                'entregado' => null,
                'envio' => null,
                'cancelado' => null,
            ][$order->state->value ?? null];
        })->groupBy(function ($order) {
            // Agrupar los pedidos por el estado general
            return $order->state->value;
        });

        // Aplicar el orden específico dentro de cada grupo de estado
        $pedidosOrdenados = collect();

        // Incompletos: Ordenados por fecha estimada, de más reciente a más antigua
        if ($pedidos->has('incompleto')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['incompleto']->sortBy('estimated_date')
            );
        }

        // Pendientes: Ordenados por fecha de creación, de más antigua a más reciente
        if ($pedidos->has('pendiente')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['pendiente']->sortBy('created_at')
            );
        }

        // Listo para retirar: Ordenados por fecha de creación, de más antigua a más reciente
        if ($pedidos->has('retirar')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['retirar']->sortBy('created_at')
            );
        }

        // Agrupar entregados, cancelados y envíos en un solo grupo y ordenarlos por fecha de creación (descendente)
        $estadosFinales = collect();
        foreach (['entregado', 'cancelado', 'envio'] as $estado) {
            if ($pedidos->has($estado)) {
                $estadosFinales = $estadosFinales->concat($pedidos[$estado]);
            }
        }
        if ($estadosFinales->isNotEmpty()) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $estadosFinales->sortByDesc('created_at')
            );
        }

        return $pedidosOrdenados;
    }

    public static function saveOrder(Request $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = Order::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos del pedido cliente');
        }

        return $order;
    }

    public function showPedido(Request $requets, $id)
    {
        $order = Order::findOrFail($id);
        if ($requets->type) {
            $method = $requets->type;
            return sendResponse(OrderResource::$method($order));
        }
        return sendResponse(new OrderResource($order, 'complete'));
    }

    /*  public function showPedido($id)
    {
        $order = Order::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    } */

    public function updateState(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($request->order_id);

            if ($order->shipment) {
                return sendResponse(null, 'Ya existe un envio creado con este pedido', 300);
            }
            $estado = Table::find($request->state_id);

            //app()[PermissionRegistrar::class]->forgetCachedPermissions();

            //$user = User::find(auth()->user()->id);

            /* if ($estado->value === 'entregado' && !$user->can('pedido.estado.entregado')) {
                return sendResponse(null, "Acción no autorizada");
            } else if ($estado->value === 'cancelado' && !$user->can('pedido.estado.cancelado')) {
                return sendResponse(null, "Acción no autorizada");
            } */

            $type = $order->type->value;

            $general_state = $order->state;

            $entregado = Table::where('name', "order_{$type}_state")->where('value', 'entregado')->first();
            $cancelado = Table::where('name', "order_{$type}_state")->where('value', 'cancelado')->first();

            if ($general_state->id == $entregado->id || $general_state->id == $cancelado->id) {
                return sendResponse(null, "El pedido se encuentra $general_state->value ", 301);
            }

            $detail = OrderProduct::where('order_id', $request->order_id)->get();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado */
                if ($item->state_id != $entregado->id && $item->state_id != $cancelado->id) {
                    $item->state_id = (int)$request->state_id;
                    $item->save();
                }
            }

            activity("pedido.$estado->value")
                ->performedOn($order)
                ->withProperties(['state_id' => $request->state_id])
                ->log($request->motivo ? $request->motivo : "Pedido $estado->value");

            $order = Order::find($request->order_id);

            $order->state_id = $request->state_id;
            $order->save();
            /* Envio de email */
            $this->sendEmail($estado, $type, $order);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    private function sendEmail($estado, $type, $order)
    {

        try {
            if (app()->environment('local')) {
                return null;
            }

            if ($estado->value === 'retirar' && $type == 'cliente') {
                TraitPedidosEmail::pedidoUnicoRetirar($order);
            } else if ($estado->value === 'retirar' && $type == 'online') {
                TraitPedidosEmail::pedidoRetirar($order);
            } else if ($estado->value === 'entregado' && $type == 'cliente') {
                TraitPedidosEmail::pedidoEntregado($order);
            } else if ($estado->value === 'entregado' && $type == 'online') {
                TraitPedidosEmail::pedidoOnlineEntregado($order);
            } else if ($estado->value === 'cancelado' && $type == 'online') {
                TraitPedidosEmail::pedidoCancelado($order);
            }
        } catch (\Throwable $th) {
            activity("error.email")
                ->performedOn($order)
                ->withProperties($th->getTrace())
                ->log($th->getMessage());
        }
    }

    public function updatePedido(UpdateOrderRequest $request, int $id)
    {
        $type = Table::find($request->type_id);

        if ($type->value == 'cliente') {
            $pedido = PedidoCliente::findOrFail($id);
        } else if ($type->value == 'online') {
            $pedido = PedidoOnline::findOrFail($id);
        } else if ($type->value == 'siniestro') {
            $pedido = Siniestro::findOrFail($id);
        }

        $pedido->fill($request->all())->save();
        return sendResponse(OrderResource::toForm($pedido));
    }

    public function productos(Request $request)
    {
        $orderProducts = $this->getProductosPedidosQuery()->get();

        $products = $orderProducts->map(function (OrderProduct $orderProduct) {
            $order = $orderProduct->order;

            return ProductResource::order(
                $orderProduct->product,
                $orderProduct,
                false,
                $order->state
            );
        });

        $priority = [
            'incompleto' => 1,
            'pendiente'  => 2,
            'retirar'    => 3,
            'entregado'  => 4,
            'cancelado'  => 5,
            'envio'      => 6,
        ];

        $products = $products
            ->sortBy(function ($product) use ($priority) {

                if (
                    !isset($product['order_state']) ||
                    !is_object($product['order_state']) ||
                    !isset($product['order_state']->value)
                ) {
                    return 99;
                }

                $value = $product['order_state']->value;

                return isset($priority[$value]) ? $priority[$value] : 99;
            })
            ->values()
            ->take($limit = 3000);

        return sendResponse($products);
    }

    public function productos_search(Request $request)
    {
        try {
            $query = OrderProduct::with(['product', 'order']);
            foreach ($request->all() as $key => $value) {
                if (!$value) {
                    continue;
                }

                switch ($key) {
                    case 'brand':
                        $query->whereHas('product.brand', function ($q) use ($value) {
                            $q->where('name', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'engine':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('engine', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'description':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('description', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'equivalence':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('equivalence', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'factory_code':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('factory_code', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'model':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('model', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'provider_code':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('provider_code', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case '_id':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->where('code', 'LIKE', '%' . $value . '%');
                        });
                        break;

                    case 'ubication':
                        $query->whereHas('product', function ($q) use ($value) {
                            $q->whereRaw(
                                "CONCAT(ship, module, side, LPAD(`column`, 2, '0'), LPAD(`row`, 2, '0')) LIKE ?",
                                ['%' . $value . '%']
                            );
                        });
                        break;

                    case 'order_date':
                        applyDateFilter($query, 'created_at', $value);
                        break;

                    default:
                        $query->where($key, 'LIKE', '%' . $value . '%');
                        break;
                }
            }

            $products = $query->get()->map(function ($orderProduct) {
                return ProductResource::order($orderProduct->product, $orderProduct);
            });

            $products = $products->sortBy(function ($product) {
                return [
                    'incompleto' => 1,
                    'pendiente' => 2,
                    'retirar' => 3,
                    'entregado' => 4,
                    'cancelado' => 5,
                    'envio' => 6,
                ][$product['order_state']->value];
            })->values();

            return sendResponse($products);
        } catch (\Exception $th) {
            return sendResponse(null, $th->getMessage(), 301);
        }
    }
}
