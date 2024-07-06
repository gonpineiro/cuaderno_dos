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
        <td class="width-5">{{$item['amount']}}</td>
        <td class="width-10">{{$item['code']}}</td>
        <td class="width-50">{{$item['description'] }}</td>
        <td class="width-15">{{$item['unit_price']}}</td>
        <td class="width-20">{{$item['total']}} </td>
    </tr>
    @endforeach
</table>


<p class="w-100 importe">
    TOTAL A PAGAR CONTADO: <span class="total">{{$total}}</span>
</p>
@endsection
