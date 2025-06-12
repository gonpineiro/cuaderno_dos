@extends('emails.layout')

@section('content')

<p>

    Su pedido online fue ENTREGADO.
</p>

<p>
    Le dejamos un link para que califique públicamente su experiencia en nuestro local:
</p>

<br>

<br>
@component('components.email.opinar')@endcomponent
<br>
<br>

@component('components.email.gracias')@endcomponent

@endsection