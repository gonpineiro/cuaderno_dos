@extends('emails.layout')

@section('content')

<p>

    Su pedido fue ENTREGADO.
</p>

<p>
    Le dejamos una breve encuesta de satisfacción que nos ayuda a seguir mejorando en el siguiente botón:
</p>

<br>

<br>
@component('components.email.encuesta-and-gracias')@endcomponent

@endsection