@extends('emails.layout')

@section('content')


<p>Estimado Cliente: {{$pedido->client->name}}</p>

<p>
    Se ha vencido el plazo para retirar su pedido en Allende Repuestos.
</p>

<p>
    En caso de querer renovar el tiempo para retirar comuniquese al whatsapp por favor.
</p>

@component('components.email.horarios')@endcomponent

Saludos.

@endsection
