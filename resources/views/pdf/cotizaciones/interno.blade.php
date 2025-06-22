@extends('pdf.cotizaciones.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th style="width: 5%">Código</th>
        <th style="width: 5%">Cod. Proveedor</th>
        <th>Descripción</th>
        <th style="width: 7%">Cant.</th>
        <th style="width: 11%">Precio U.</th>
        <th style="width: 15%">Total</th>
        <th style="width: 5%">Ubicación</th>
    </tr>

    @foreach ($detail as $item)
    <tr>
        <td>{{$item['code']}}</td>
        <td>{{$item['provider_code']}}</td>
        <td>{{$item['description']}}</td>
        <td>{{$item['amount']}}</td>
        <td>{{$item['unit_price']}}</td>
        <td>{{$item['total']}} </td>
        <td>{{$item['ubication']}}</td>
    </tr>
    @endforeach
</table>


@endsection
{{-- Total --}}
{{-- <p class="w-100 importe">
    IMPORTE TOTAL {{$is_contado ? 'IMPORTE TOTAL PRECIO CONTADO' : ''}}: <span class="total">{{$total}}</span>
</p>
--}}
