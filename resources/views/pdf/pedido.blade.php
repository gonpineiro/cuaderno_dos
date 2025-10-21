<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
</head>

<style>
    .bold {
        font-weight: bold;
        font-size: 0.7rem;
    }

    /* Agrega más definiciones para otros pesos y estilos de la fuente Roboto */
    table {
        width: 100%;
        font-size: 1.1rem;
    }

    .table-productos {
        border: 1px solid rgba(0, 0, 0, .125);
    }

    .table-productos tr,
    .table-productos tr th,
    .table-productos tr td {
        border: 1px solid rgba(0, 0, 0, .125);
        font-size: 0.8rem;
    }

    .importe, p {
        margin-top: 16px;
        font-size: 1.3rem;
    }

    .total {
        font-weight: 500;
    }
</style>

<body>
    <div class="card px-3" style="margin-bottom: 50px">
        <table>
            <tr>
                <td colspan="3">
                    <h1>ID: {{$pedido->id}}</h1>
                </td>
            </tr>
            <hr>
            <tr>
                <td><strong>Nombre y apellido:</strong></td>
                <td>{{$pedido->client->name}}</td>
            </tr>

            @if ($pedido->client->cuit)
            <tr>
                <td><strong>CUIT:</strong></td>
                <td>{{$pedido->client->cuit}}</td>
            </tr>
            @endif

            @if ($pedido->client->dni)
            <tr>
                <td><strong>DNI:</strong></td>
                <td>{{$pedido->client->dni}}</td>
            </tr>
            @endif

            <tr>
                <td><strong>Teléfono:</strong></td>
                <td>{{$pedido->client->phone}}</td>
            </tr>
            <tr>
                <td><strong>Forma de pago:</strong></td>
                <td>{{$pedido->payment_method->description}}</td>
            </tr>
            <tr>
                <td><strong>Vehículo:</strong></td>
                <td>{{$pedido->price_quote->vehiculo->name}}</td>
            </tr>
            <tr>
                <td><strong>Vendedor:</strong></td>
                <td>{{$pedido->user->name}}</td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong></td>
                <td>{{$fecha}}</td>
            </tr>
        </table>
        <hr>
        <h3>Observaciones</h3>
        <p>{{$pedido->observation}}</p>
    </div>

    <table class="table-productos">
        <tr>
            <th style="width: 13%">Código</th>
            <th style="width: 12%">Cod. Prov.</th>
            <th style="width: 10%">C. Fabrica</th>
            <th style="width: 5%">Ubicación</th>
            <th>Descripcion</th>
            <th style="width: 5%">Cant.</th>
            <th style="width: 11%">Precio U.</th>
            <th style="width: 13%">Total</th>
        </tr>


        @foreach ($detail as $item)
        <tr>
            <td>{{$item['code']}}</td>
            <td>{{$item['provider_code']}}</td>
            <td>{{$item['factory_code']}}</td>
            <td>{{$item['ubication']}}</td>
            <td>{{$item['description']}}</td>
            <td>{{$item['amount']}}</td>
            <td>{{$item['unit_price']}}</td>
            <td>{{$item['total']}}</td>
        </tr>
        @endforeach
    </table>
    <hr>
    <p class="w-100 importe">
        @if ($pedido->payment_method && $pedido->payment_method->value === 'online')
        PAGADO ONLINE:
        @else
        IMPORTE TOTAL {{strtoupper($cotizacion->type_price->value)}}:
        @endif

        <span class="total">{{$total}}</span>
    </p>
    @if (isset($pedido->deposit))

    <p class="">
        SEÑA: <span class="total">{{$deposit}}</span>
    </p>
    <p class="">
        DIFERENCIA:ss <span class="total">{{$diferencia}}</span>
    </p>
    @endif
</body>

</html>
