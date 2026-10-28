<?php

namespace Tests\Feature;

use Tests\TestCase;

class PriceQuotePdfPriceTypeTest extends TestCase
{
    public function test_list_price_does_not_show_tax_legend(): void
    {
        $cotizacion = (object) [
            'type_price' => (object) ['value' => 'lista'],
            'client' => (object) [
                'condicion_iva' => (object) ['value' => 'resp_incripto'],
            ],
            'observation' => '',
        ];

        $views = [
            'pdf.cotizaciones.partials.products-table',
            'pdf.cotizaciones.partials.total-products-table',
        ];

        foreach ($views as $view) {
            $html = view($view, [
                'cotizacion' => $cotizacion,
                'detail' => [],
                'iva' => null,
                'total' => 0,
            ])->render();

            $this->assertStringContainsString('LISTA', $html);
            $this->assertStringNotContainsString('PRECIO SIN IVA', $html);
        }
    }
}
