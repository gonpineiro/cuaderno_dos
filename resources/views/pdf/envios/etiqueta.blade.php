@extends('pdf.envios.layout')

@section('content')

<style>
    .card {
        width: 90%;
        margin: auto;
        text-align: center;
    }

    .container {
        height: 100%;
    }

    td {
        font-size: 25px !important;
    }
</style>

<div class="container">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="card px-3">
        <table>
            <tr>
                <td>{{$cotizacion->client->name}}</td>
            </tr>
            <tr>
                <td style="text-align: right;">{{$total}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->client->city->name}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->client->city->province->description}}</td>
            </tr>
            <tr>
                <td>{{$cotizacion->bultos}}</td>
            </tr>
        </table>
    </div>
</div>

@endsection
