@extends('pdf.cotizaciones.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th style="width: 50%">Descripción</th>
        <th>Cant.</th>
        <th>Precio U.</th>
        <th>Total</th>
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


{{-- Total --}}
<p class="w-100 importe">
    IMPORTE TOTAL {{$is_contado ? 'PRECIO CONTADO' : ''}}: <span class="total">{{$total}}</span>
</p>
@endsection

