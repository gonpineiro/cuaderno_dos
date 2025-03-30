<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Shipment;

class EnvioDespachadoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $shipment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Producto Despachado - ' . env('APP_NAME'))
            ->view('emails.envios.envio_despachado')
            ->with([
                'shipment' => $this->shipment,
            ]);
    }
}
