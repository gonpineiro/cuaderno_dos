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
    @if ($type != 'interno')
    <div class="images">
        <img src="{{ public_path('assets/images/logoallende.png') }}" style="width: 40%; margin-right: 50px">
        <img src="{{ public_path('assets/images/fiat.png') }}" style="width: 8%; margin-right: 20px">
        <img src="{{ public_path('assets/images/peugeot.png') }}" style="width: 13%; margin-right: 20px">
        <img src="{{ public_path('assets/images/renault.png') }}" style="width: 15%; margin-right: 20px">
    </div>
    <hr>
    <table>
        <tr>
            <td style="vertical-align: middle;" colspan="2"><img src="{{ public_path('assets/images/map.jpg') }}"
                    style="width: 25px"> <span style="vertical-align: middle;">25 de Mayo 373
                    | Cipolletti, Río Negro</span>
            </td>
            <td style="vertical-align: middle;"><img src="{{ public_path('assets/images/fb.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;">/repuestosallende</span>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><img src="{{ public_path('assets/images/tel.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;"> 0299 4781525</span>
            </td>
            <td style="vertical-align: middle;"><img src="{{ public_path('assets/images/wapp.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;">2995935575</span>
            </td>
            <td style="vertical-align: middle;"><img src="{{ public_path('assets/images/ig.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;"> {{"@allende_repuestos"}}
                </span>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;" colspan="2"><img src="{{ public_path('assets/images/mail.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;">contacto@allenderepuestos.com.ar</span>
            </td>
            <td style="vertical-align: middle;"><img src="{{ public_path('assets/images/web.png') }}"
                    style="width: 25px"> <span style="vertical-align: middle;">www.allenderepuestos.com.ar/</span>
            </td>
        </tr>
    </table>
    <hr>
    @endif

    <div class="card px-3" style="margin-bottom: 50px">
        <table>
            <tr>
                <td colspan="3">
                    <h1>Número de cotizacion: {{$cotizacion->id}}</h1>
                </td>
            </tr>
            <hr>
            <tr>
                <td class="bold">Fecha:</td>
                <td>{{$cotizacion->created_at}}</td>
            </tr>
            <tr>
                <td class="bold">Cliente:</td>
                <td>{{$cotizacion->client->name}}</td>
            </tr>
            <tr>
                <td class="bold">Teléfono:</td>
                <td>{{$cotizacion->client->phone}}</td>
            </tr>
            <tr>
                <td class="bold">Vehículo:</td>
                <td>{{$cotizacion->engine}}</td>
            </tr>
            <tr>
                <td class="bold">{{$cotizacion->type_price}}</td>
            </tr>
        </table>
        <hr>
        <h3>Observaciones</h3>
        <p>{{$cotizacion->observation}}</p>
    </div>

    <table class="table-productos">
        <tr>
            <th style="width: 50%">Descripción</th>
            <th>Cant.</th>
            <th>Precio U.</th>
            <th>Total</th>
        </tr>


        @foreach ($detail as $item)
        <tr>
            <td>{{$item->description}}</td>
            <td>{{$item->amount}}</td>
            <td>$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td>$ {{ number_format($item->unit_price * $item->amount, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    <hr>
    @if (isset($total) && $total)
    <p class="w-100 text-center total">
        IMPORTE TOTAL : $ {{number_format($total, 2, ',', '.')}}
    </p>
    @endif
</body>

</html>