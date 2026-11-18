<?php

namespace Tests\Feature;

use Tests\TestCase;

class PriceQuotePdfPriceTypeTest extends TestCase
{
    public function test_financing_pdf_hides_current_account_and_counter_payment_rows(): void
    {
        $html = view('pdf.cotizaciones.calculadora', [
            'type' => 'interno',
            'cotizacion' => (object) [
                'id' => 1,
                'created_at' => now(),
                'client' => (object) ['name' => '', 'phone' => ''],
                'vehiculo' => (object) ['name' => ''],
                'type_price' => (object) ['value' => 'lista'],
                'observation' => '',
                'version' => null,
                'patente' => null,
            ],
            'coefs' => [
                ['description' => '6 CUOTAS', 'price' => '$ 600', 'valor_cuota' => '$ 100'],
                ['description' => 'Cuenta corriente', 'price' => '$ 500', 'valor_cuota' => ' '],
                ['description' => 'MOSTRADOR', 'price' => '$ 500', 'valor_cuota' => ' '],
            ],
        ])->render();

        $this->assertStringContainsString('6 CUOTAS', $html);
        $this->assertStringNotContainsString('Cuenta corriente', $html);
        $this->assertStringNotContainsString('MOSTRADOR', $html);
    }

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
