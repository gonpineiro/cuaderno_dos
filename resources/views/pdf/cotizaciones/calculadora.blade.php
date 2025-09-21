@extends('pdf.cotizaciones.layout')

@section('content')
    <div class="wrapper" style="margin:0;margin-top: -2px;">
        <h2>Tipos de financiación</h2>
        <table class="table-productos">
            <tr>
                <th>CANTIDAD CUOTAS</th>
                <th>PRECIO</th>
                <th>VALOR CUOTA</th>
            </tr>

            @foreach ($coefs as $coef)
                <tr>
                    <td>{{ $coef['description'] }}</td>
                    <td>{{ $coef['price'] }}</td>
                    <td>{{ $coef['valor_cuota'] }}</td>
                </tr>
            @endforeach
        </table>
        <h3 class="importe" style="padding: 15px 5px;">
            <b> No trabajamos Confiable, Crediguia y Credicom </b>
        </h3>
    </div>
@endsection
