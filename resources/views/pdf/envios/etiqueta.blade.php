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

    .r {
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
                <td class="cliente">{{$shipment->client->name}}</td>
            </tr>
            <tr>
                <td class="cliente">{{$shipment->client->adress}}</td>
                <td class="r">{{$total}}</td>
            </tr>
            <tr>
                <td>{{$shipment->client->city->name}}</td>
                <td class="r">{{$shipment->bultos}}</td>
            </tr>
            <tr>
                <td>{{$shipment->client->city->province->name}}</td>
            </tr>
        </table>
    </div>
</div>

@endsection
