@extends('pdf.envios.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th style="width: 7%">Cant.</th>
        <th style="width: 10%">Código</th>
        <th>Descripción</th>
        <th style="width: 11%">Precio U.</th>
        <th style="width: 15%">Total</th>
    </tr>

    @foreach ($detail as $item)
    <tr>
        <td>{{$item['amount']}}</td>
        <td>{{$item['code']}} </td>
        <td>{{$item['description'] }}</td>
        <td>{{$item['unit_price']}}</td>
        <td>{{$item['total']}} </td>
    </tr>
    @endforeach
</table>


<p class="w-100 importe">
    TOTAL A PAGAR CONTADO: <span class="total">{{$total}}</span>
</p>
@endsection
