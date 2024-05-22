@extends('pdf.envios.layout')

@section('content')

<hr>
{{-- Detalle productos --}}
<table class="table-productos">
    <tr>
        <th>Cant.</th>
        <th>Código</th>
        <th>Descripción</th>
        <th>Precio U.</th>
        <th>Total</th>
    </tr>

    @foreach ($detail as $item)
    <tr>
        <td>{{$item['amount']}}</td>
        <td>{{$item['code']}}</td>
        <td>{{$item['description']}}</td>
        <td>{{$item['unit_price']}}</td>
        <td>{{$item['total']}} </td>
    </tr>
    @endforeach
</table>


<p class="w-100 importe">
    TOTAL A PAGAR CONTADO: <span class="total">{{$total}}</span>
</p>
@endsection
