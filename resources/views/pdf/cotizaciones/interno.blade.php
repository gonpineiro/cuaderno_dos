@extends('pdf.cotizaciones.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th>Código</th>
        <th>Cant.</th>
        <th>Precio U.</th>
        <th>Total</th>
        <th>Ubicación</th>
    </tr>

    @foreach ($detail as $item)
    <tr>
        <td>{{$item['code']}}</td>
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
