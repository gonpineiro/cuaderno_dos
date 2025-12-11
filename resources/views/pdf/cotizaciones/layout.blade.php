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
        margin-bottom: 16px !important;
    }

    .table-productos tr,
    .table-productos tr th,
    .table-productos tr td {
        border: 1px solid rgba(0, 0, 0, .125);
        font-size: 0.8rem;
    }

    .importe {
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
    <div class="card px-3" style="margin-bottom: 40px">
        <table>
            <tr>
                <td colspan="3">
                    <h2>COTIZACION N°: {{$cotizacion->id}}</h2>
                </td>
            </tr>
            <hr>
            <tr>
                <td class="bold">Fecha:</td>
                <td>{{date("d/m/Y", strtotime($cotizacion->created_at))}}</td>
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
                <td>{{$cotizacion->vehiculo->name}}</td>
            </tr>
            @if (!!$cotizacion->client->is_insurance)
            <tr>
                <td class="bold">Version:</td>
                <td>{{$cotizacion->version}}</td>
            </tr>
            <tr>
                <td class="bold">Patente:</td>
                <td>{{$cotizacion->patente}}</td>
            </tr>
            @endif
            <tr>
                <td class="bold">Tipo Precio:</td>
                <td>{{$cotizacion->type_price->value == 'contado' ? 'Contado / debito /
                    tarjeta 1 pago. IVA INCLUIDO' : 'Lista'}}</td>
            </tr>
            <tr>
                <td colspan="3"><small>Precios sujeto a modificación sin previo aviso</small></td>
            </tr>
        </table>
        <hr>
        <h3>Observaciones</h3>
        <p>{{$cotizacion->observation}}</p>
    </div>
    @yield('content')
</body>

</html>