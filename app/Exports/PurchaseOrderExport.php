<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $detail;

    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    public function collection()
    {
        return $this->detail;
    }

    public function headings(): array
    {
        return ['CANTIDAD', 'CÓDIGO', 'DESCRIPCIÓN']; // Aquí defines los encabezados de la hoja de Excel
    }

    public function map($order): array
    {
        $t = $order->product;
        return [
            $order->amount,
            $order->product->provider_code ?? $order->product->code,
            $order->product->description,
        ];
    }
}
