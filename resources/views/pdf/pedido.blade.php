<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
</head>

<style>
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
    }

    .total {
        font-size: 2rem;
        font-weight: 500;
        text-decoration: underline;
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
                    <td>Nombre y apeliido:</td>
                    <td>{{$pedido->client->name}}</td>
                </tr>
                <tr>
                    <td>Teléfono:</td>
                    <td>{{$pedido->client->phone}}</td>
                </tr>
                <tr>
                    <td>Forma de pago:</td>
                    <td>{{$pedido->payment_method}}</td>
                </tr>
                <tr>
                    <td>Vehículo:</td>
                    <td>{{$pedido->engine}}</td>
                </tr>
            </table>
            <hr>
            <h3>Observaciones</h3>
            <p>{{$pedido->observation}}</p>
        </div>

        <table class="table-productos">
            <tr>
                <th>Código</th>
                <th style="width: 50%">Descripcion</th>
                <th>Cant</th>
                <th>Precio U.</th>
                <th>Total</th>
            </tr>


            @foreach ($detail as $item)
            <tr>
                <td>{{$item->product->code}}</td>
                <td>{{'Descripción del producto'}}</td>
                <td>{{$item->amount}}</td>
                <td>{{$item->unit_price}}</td>
                <td>{{$item->unit_price * $item->amount}}</td>
            </tr>
            @endforeach
        </table>
        <hr>
        <p class="w-100 text-center total">
            IMPORTE TOTAL : $3324,32
        </p>
    </div>
</body>

</html>
