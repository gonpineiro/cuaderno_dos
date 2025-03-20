@extends('emails.layout')

@section('content')

<h1>Nos ponemos en contacto con usted ya que tenemos novedades acerca de su pedido en Allende Repuestos.</h1>

<p>

    Su pedido {{$pedido->id}} se encuentra disponible en el local de Allende Repuestos para ser retirado.
</p>

<p>
    Horarios:
</p>

<p>

    Lunes a Viernes de 9.00 a 18.00 hs.
</p>

<p>

    Sábados de 9.00 a 13.00 hs.
</p>

<p>

    Dirección: 25 de mayo 373, Cipolletti.
</p>
<br>

Saludos.
<div class="hr"> </div>

<div class="footer">
    <a href="http://www.allenderepuestos.com.ar/promociones/" target="_blank">
        <img src="https://mcusercontent.com/d30eb551b1cbc7139f2bb7691/images/0cc49c36-7530-5dfe-b416-8c4f7f1b2d26.jpg"
            width="550">
    </a>
</div>

@endsection
