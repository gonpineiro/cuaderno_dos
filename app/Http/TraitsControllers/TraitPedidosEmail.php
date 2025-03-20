<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Product\ProductResource;
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
use App\Mail\CrearPedidoProductoUnicoEmail;
use App\Mail\PedidoRetirarEmail;
use Illuminate\Support\Facades\Mail;

trait TraitPedidosEmail
{
    public function pedidoProductoUnico(Order $pedido)
    {        
        $correo = new CrearPedidoProductoUnicoEmail($pedido);       
        Mail::to('gon.pineiro@gmail.com')->send(new CrearPedidoProductoUnicoEmail($pedido));             
    }

    public function pedidoRetirar(Order $pedido)
    {        
        $correo = new PedidoRetirarEmail($pedido);        
        Mail::to('gon.pineiro@gmail.com')->send(new PedidoRetirarEmail($pedido));             
    }
}
