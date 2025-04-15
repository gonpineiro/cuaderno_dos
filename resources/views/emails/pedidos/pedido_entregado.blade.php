@extends('emails.layout')

@section('content')

<p>

    Su pedido fue ENTREGADO.
</p>

<p>
    Le dejamos un link para que califique públicamente su experiencia en nuestro local:
</p>

<br>

<br>
@component('components.email.encuesta-and-gracias')@endcomponent

@endsection