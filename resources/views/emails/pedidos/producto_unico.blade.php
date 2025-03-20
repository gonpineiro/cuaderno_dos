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
        <td>{{$item['amount']}}</td>
        <td>{{$item['description']}}</td>
        <td>{{$item['unit_price']}}</td>
        <td>{{$item['total']}} </td>
    </tr>
    @endforeach

</table>

<br>

<p>IMPORTE TOTAL: <strong> {{$total}}</strong> </p>
<p>SEÑA: <strong> {{$deposit}}</strong> </p>
<p>RESTA PAGAR: <strong> {{$resto}}</strong> </p>
<!-- <p>FECHA ESTIMADA DE DEMORA: <strong> asdasd</strong> </p> -->

<p>NOS PONDREMOS EN CONTACTO CON USTED CUANDO TENGAMOS NOVEDADES DE LOS PRODUCTOS.</p>

<div class="hr"> </div>

<div class="footer">
    <a href="http://www.allenderepuestos.com.ar/promociones/" target="_blank">
        <img src="https://mcusercontent.com/d30eb551b1cbc7139f2bb7691/images/0cc49c36-7530-5dfe-b416-8c4f7f1b2d26.jpg"
            width="550">
    </a>
</div>

@endsection
