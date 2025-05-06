@extends('emails.layout')

@section('content')

<p>Estimado Cliente: <strong> {{$pedido->client->name}}</strong></p>

<p>HA REALIZADO EL SIGUIENTE PEDIDO EN ALLENDE REPUESTOS: </p>

<p>ID PEDIDO: <strong> {{$pedido->id}}</strong> </p>
<p>VEHICULO: <strong> {{$pedido->vehiculo->name}}</strong> </p>

<br>

<table class="table">
    <tr>
        <th>CANTIDAD</th>
        <th>DESCRIPCION</th>
        <th>PRECIO UNITARIO</th>
        <th>IMPORTE</th>
    </tr>
    @foreach ($detail as $item)
    <tr>
        <td class="td">{{$item['amount']}}</td>
        <td class="td">{{$item['description']}}</td>
        <td class="td">{{$item['unit_price']}}</td>
        <td class="td">{{$item['total']}} </td>
    </tr>
    @endforeach

</table>

<br>

<p>IMPORTE TOTAL: <strong> {{$total}}</strong> </p>
<p>SEÑA: <strong> {{$deposit}}</strong> </p>
<p>RESTA PAGAR: <strong> {{$resto}}</strong> </p>
<p>FECHA ESTIMADA: <strong>{{ \Carbon\Carbon::parse($pedido->estimated_date)->format('d/m/Y') }}</strong> </p>


<p>NOS PONDREMOS EN CONTACTO CON USTED CUANDO TENGAMOS NOVEDADES DE LOS PRODUCTOS.</p>

@endsection
