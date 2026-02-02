@extends('pdf.cotizaciones.layout')

@section('content')

<div class="table-wrap">
    <table class="table-productos">
        <tr class="table-header">
            <th>Descripción</th>
            <th>Cant.</th>
            <th>Precio U.</th>
            <th>Total</th>
        </tr>

        @foreach ($detail as $item)
        <tr>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['amount'] }}</td>
            <td>{{ $item['unit_price'] }}</td>
            <td>{{ $item['total'] }}</td>
        </tr>
        <tr>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['amount'] }}</td>
            <td>{{ $item['unit_price'] }}</td>
            <td>{{ $item['total'] }}</td>
        </tr>
        <tr>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['amount'] }}</td>
            <td>{{ $item['unit_price'] }}</td>
            <td>{{ $item['total'] }}</td>
        </tr>
        <tr>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['amount'] }}</td>
            <td>{{ $item['unit_price'] }}</td>
            <td>{{ $item['total'] }}</td>
        </tr>
        @endforeach
    </table>
    <table class="table-totales">
        <tr>
            <td class="td-spacer"></td>
            <td colspan="2" class="label-total">Subtotal</td>
            <td class="value-total">{{ formatoMoneda($total - $iva) }}</td>
        </tr>
        <tr>
            <td class="td-spacer"></td>
            <td colspan="2" class="label-total">Total</td>
            <td class="value-total">{{ formatoMoneda($iva) }}</td>
        </tr>
    </table>
</div>

@if($iva)
<p class="importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }} SIN IVA:
    <span class="total">{{ formatoMoneda($total - $iva) }}</span>
</p>

<p class="importe">
    TOTAL IVA:
    <span class="total">{{ formatoMoneda($iva) }}</span>
</p>

<p class="importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }}:
    <span class="total">{{ formatoMoneda($total) }}</span>
</p>
@else
<p class="importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }}:
    <span class="total">{{ formatoMoneda($total) }}</span>
</p>
@endif

@endsection