<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Order\OrderProductResource;

use App\Models\Order;

class CrearPedidoClienteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido; 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $detail = OrderProductResource::emailPedidoArray($this->pedido->detail);
        $total = get_total_price($this->pedido->detail);

        return $this->view('emails.pedidos.producto_unico')->with([
                        'pedido' => $this->pedido,
                        'detail' =>  OrderProductResource::formatPdf($detail),
                        'total' =>  formatoMoneda($total),
                        'deposit' => formatoMoneda($this->pedido->deposit),
                        'resto' =>formatoMoneda($total - $this->pedido->deposit)
                    ]);
    }
}