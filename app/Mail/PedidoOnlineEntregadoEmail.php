<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class PedidoOnlineEntregadoEmail extends Mailable
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
        return $this
            ->subject('Pedido online entregado - ' . env('APP_NAME'))
            ->view('emails.pedidos.pedido_online_entregado')
            ->with([
                'pedido' => $this->pedido,
            ]);
    }
}
