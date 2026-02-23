@extends('pdf.cotizaciones.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th style="width: 65%">Descripción</th>
        <th style="width: 7%">Cant.</th>
        <th style="width: 11%">Precio U.</th>
        <th style="width: 13%">Total</th>
    </tr>

    @foreach ($detail as $item)
    <tr>
        <td>{{$item['description']}}</td>
        <td>{{$item['amount']}}</td>
        <td>{{$item['unit_price']}}</td>
        <td>{{$item['total']}} </td>
    </tr>
    @endforeach
</table>



@if($iva)
<p class="w-100 importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }} SIN IVA:
    <span class="total">{{ formatoMoneda($total - $iva) }}</span>
</p>

<p class="w-100 importe">
    TOTAL IVA: <span class="total">{{ formatoMoneda($iva) }}</span>
</p>

<p class="w-100 importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }}:
    <span class="total">{{ formatoMoneda($total) }}</span>
</p>
@else
<p class="w-100 importe">
    IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }}:
    <span class="total">{{ formatoMoneda($total) }}</span>
</p>
@endif
@endsection