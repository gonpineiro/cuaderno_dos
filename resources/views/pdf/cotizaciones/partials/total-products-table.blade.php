{{-- products.blade.php --}}

@php
$_iva = $iva ? $iva : 0;

$tipo_precio = $cotizacion->type_price->value == 'contado' ? 'Contado / debito /
tarjeta 1 pago. IVA INCLUIDO' : 'Lista';
@endphp

<table class="table-productos" style="margin-top: 15px;">
    <thead>
        <tr>
            <th class="table-productos-header" style="width:65%;">DESRIPCIÓN</th>
            <th class="table-productos-header" style="width:7%; text-align:center;">CANT.</th>
            <th class="table-productos-header" style="width:11%; text-align:right;">P. UNIT.</th>
            <th class="table-productos-header" style="width:13%; text-align:right;">TOTAL</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($detail as $item)
        <tr>
            <td style="padding:4px 0px; ">
                {{ $item['description']}}
            </td>

            <td style="padding:4px 0px;  text-align:center;">
                {{ $item['amount'] }}
            </td>

            <td style="padding:4px 0px;  text-align:right;">
                {{$item['unit_price']}}
            </td>

            <td style="padding:4px 0px;  text-align:right;">
                {{$item['total']}}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        @if($iva)
        <tr>
            <td colspan="3" style="text-align:right; padding:4px 0px;">
                <strong>Subtotal</strong>
            </td>
            <td style="text-align:right; padding:4px 0px;">
                {{ formatoMoneda($total - $_iva) }}
            </td>
        </tr>

        <tr>
            <td colspan="3" style="text-align:right; padding:4px 0px;">
                <strong>IVA 21%</strong>
            </td>
            <td style="text-align:right; padding:4px 0px;">
                {{formatoMoneda($_iva)}}
            </td>
        </tr>
        @endif
        <tr>
            <td colspan="3" style="text-align:right; padding:4px 0px;">
                <strong>Importe Total</strong>
            </td>
            <td style="text-align:right; padding:4px 0px;">
                <strong>{{ formatoMoneda($total) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>

<table style="margin-top:5px;">
    <tr>
        <td style="width:100%;">
            <h3 style="margin:0; color: black;">
                Tipo Precio:
            </h3>
            {{$tipo_precio}}
        </td>
    </tr>
    <tr>
        <td><small>Precios sujeto a modificación sin previo aviso</small></td>
    </tr>
    <tr>
        <td style="width:100%;">
            <h3 style="margin:0; color: black;">
                Observaciones
            </h3>
            {{$cotizacion->observation}}
        </td>
    </tr>
</table>
