@extends('emails.layout')

@section('content')



<p>Estimado Cliente: {{$pedido->client->name}}</p>

<p>
    Su pedido online id {{$pedido->id}} ha sido cancelado ya que excedió el tiempo para ser retirado.
</p>

<p>
    Ante cualquier consulta puede comunicarse por nuestros medios habituales.
</p>

<p>
    Saludos
</p>

@component('components.email.horarios')@endcomponent

Saludos.

@endsection
