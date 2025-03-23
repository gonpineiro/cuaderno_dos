<?php

namespace App\Mail;

use App\Http\Resources\PurchaseOrder\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Shipment;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase_order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
            ->subject("Pedido {$this->purchase_order->provider->name}")
            ->view('emails.proveedor.orden_compra')
            ->with([
                'oc' => new PurchaseOrderResource($this->purchase_order, 'complete'),
            ]);
    }
}
