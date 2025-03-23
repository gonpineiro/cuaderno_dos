@extends('emails.layout')

@section('content')

<h1>Nos ponemos en contacto con usted ya que tenemos novedades acerca de su pedido en Allende Repuestos.</h1>

<p>
    Su pedido {{$pedido->id}} se encuentra disponible en el local de Allende Repuestos para ser retirado.
</p>

@component('components.email.horarios')@endcomponent

Saludos.

@endsection
