@extends('emails.layout')

@section('content')



<p>Estimado Cliente: {{$pedido->client->name}}</p>

<p>
    Tenemos listo su pedido para ser <strong>retirado</strong> en el local de Allende Repuestos.
</p>

<p>
    Podes hacerlo sacando el turno de compras online en nuestro local y retira con el n° de pedido ID: {{$pedido->id}}
    ,dentro de las 48 hs hábiles.
</p>

@component('components.email.horarios')@endcomponent

Saludos.

@endsection
