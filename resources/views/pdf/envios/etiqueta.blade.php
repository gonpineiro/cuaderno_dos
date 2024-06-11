@extends('pdf.envios.layout')

@section('content')

<style>
    .etiqueta {
        width: 65%;
        margin: auto;
        text-align: center;
        padding: 1rem 6.4rem;
        margin-top: 1.1rem;
    }

    .container {
        height: 100%;
    }

    td {
        font-size: 18px !important;
    }

    .total {
        text-align: right;
        margin-right: 3rem;
    }
</style>

<div class="container">
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="etiqueta">
        <table>
            <tr>
                <td class="cliente">{{$cotizacion->client->name}}</td>
            </tr>
            <tr>
                <td class="total">{{$total}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->client->city->name}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->client->city->province->name}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->bultos}}</td>
            </tr>
        </table>
    </div>
</div>

@endsection
