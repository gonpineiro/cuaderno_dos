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

Saludos.

@endsection
