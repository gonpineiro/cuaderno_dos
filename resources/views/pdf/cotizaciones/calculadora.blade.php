@extends('pdf.cotizaciones.layout')

@section('content')

<h2>Tipos de financiación</h2>
<table class="table-productos">
    <tr>
        <th>CANTIDAD CUOTAS</th>
        <th>PRECIO</th>
        <th>VALOR CUOTA</th>
    </tr>

    @foreach ($coefs as $coef)
    <tr>
        <td>{{ $coef['description']}}</td>
        <td>{{ $coef['price']}}</td>
        <td>{{ $coef['valor_cuota']}}</td>
    </tr>
    @endforeach
</table>

<h3 class="importe">
    <b> No trabajamos Confiable, Crediguia y Credicom </b>
</h3>
@endsection
