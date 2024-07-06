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
        width: 100%
    }

    .table-productos {
        border: 1px solid rgba(0, 0, 0, .125);
    }

    .table-productos tr,
    .table-productos tr th,
    .table-productos tr td {
        border: 1px solid rgba(0, 0, 0, .125);
        font-size: 0.9rem;
    }

    .importe {
        margin-top: 16px;
        font-size: 1.2rem;
    }

    .total {
        font-weight: 500;
    }
</style>

<body>
    <div id="login">
        <div class="card px-3" style="margin-bottom: 50px">
            <table>
                <tr>
                    <td colspan="3">
                        <h1>Número de pedido: {{$pedido->id}}</h1>
                    </td>
                </tr>
                <hr>
                <tr>
                    <td>Nombre y apellido:</td>
                    <td>{{$pedido->client->name}}</td>
                </tr>
                <tr>
                    <td>Teléfono:</td>
                    <td>{{$pedido->client->phone}}</td>
                </tr>
                <tr>
                    <td>Forma de pago:</td>
                    <td>{{$pedido->payment_method->description}}</td>
                </tr>
                <tr>
                    <td>Vehículo:</td>
                    <td>{{$pedido->price_quote->vehiculo->name}}</td>
                </tr>
            </table>
            <hr>
            <h3>Observaciones</h3>
            <p>{{$pedido->observation}}</p>
        </div>

        <table class="table-productos">
            <tr>
                <th>Código</th>
                <th>Ubicación</th>
                <th>Descripcion</th>
                <th>Cant</th>
                <th>Precio U.</th>
                <th>Total</th>
            </tr>


            @foreach ($detail as $item)
            <tr>
                <td>{{$item['code']}}</td>
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
            IMPORTE TOTAL: <span class="total">{{$total}}</span>
        </p>
    </div>
</body>

</html>
