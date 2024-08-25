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
    }

    /* Agrega más definiciones para otros pesos y estilos de la fuente Roboto */
    table {
        width: 100%
    }

    .table-productos {
        border: 1px solid rgba(0, 0, 0, .125);
        font-size: 0.8rem;
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

    .final-p {
        margin-top: 15px;
        text-align: center;
        font-size: 1.5rem;
    }

    .width-5 {
        width: 5%;
    }

    .width-10 {
        width: 10%;
    }

    .width-20 {
        width: 20%;
    }

    .width-15 {
        width: 15%;
    }

    .width-50 {
        width: 50%;
    }


</style>

<body>
    @if ($type === 'orden_compra')
    <div class="card px-3" style="margin-bottom: 40px">
        <table>
            <tr>
                <td colspan="3">
                    <h1>Número de envío: {{$cotizacion->id}}</h1>
                </td>
            </tr>
            <hr>
            <tr>
                <td class="bold">Cliente:</td>
                <td>{{$cotizacion->client->name}}</td>
            </tr>
            {{-- <tr>
                <td class="bold">Fecha:</td>
                <td>{{date("d/m/Y", strtotime($cotizacion->created_at))}}</td>
            </tr> --}}
            <tr>
                <td class="bold">Dirección:</td>
                <td>{{$cotizacion->send_adress}}</td>
            </tr>
            <tr>
                <td class="bold">Ciudad:</td>
                <td>{{$cotizacion->client->city->name}}</td>
            </tr>
            <tr>
                <td class="bold">Teléfono:</td>
                <td>{{$cotizacion->client->phone}}</td>
            </tr>
            <tr>
                <td class="bold">Forma de pago:</td>
                <td>{{$cotizacion->payment_method->description}}</td>
            </tr>
            <tr>
                <td class="bold">Bultos:</td>
                <td>{{$cotizacion->bultos}}</td>
            </tr>
        </table>
        <hr>
        <h3>Observaciones</h3>
        <p>{{$cotizacion->observation}}</p>
    </div>
    @endif

    @yield('content')
</body>

</html>
