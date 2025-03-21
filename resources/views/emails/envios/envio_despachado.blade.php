@extends('emails.layout')

@section('content')


<p>Estimado Cliente: <strong>
    {{$shipment->client->name}}
</strong></p>

<p>Su pedido ha sido despachado por transporte <strong>
    {{$shipment->transport}}
</strong> y su N° de guía es: <strong>
    {{$shipment->nro_guia}}
</strong>.</p>

<p>Ante cualquier consulta no dude en comunicarse con nosotros.</p>

<p>Le dejamos una breve encuesta de satisfacción que nos ayuda a seguir mejorando en el siguiente botón:</p>


<br>

<strong>¡Gracias por su compra!</strong>


@endsection
