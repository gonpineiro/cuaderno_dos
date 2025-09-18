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
        width: 100%;
        font-size: 0.9rem;
    }

    .table-productos {
        border: 1px solid rgba(0, 0, 0, .125);
    }

    .table-productos tr,
    .table-productos tr th,
    .table-productos tr td {
        border: 1px solid rgba(0, 0, 0, .125);
        font-size: 0.7rem;
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
                <td>
                    <strong>Fecha:</strong> {{date("d/m/Y", strtotime($cotizacion->created_at))}}
                </td>
                <td>
                    <strong>Vehículo: </strong>{{$cotizacion->vehiculo->name}}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Cliente:</strong> {{$cotizacion->client->name}}
                </td>
                <td>
                    <strong>Condición Venta:</strong> {{$cotizacion->type_price->value == 'contado' ? 'Contado / debito
                    /
                    tarjeta 1 pago. IVA INCLUIDO' : 'Lista'}}
                </td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> {{$cotizacion->contacto}}</td>
            </tr>
            @if (!!$cotizacion->client->is_insurance)
            <tr>
                <td><strong>Version:</strong> {{$cotizacion->version}}</td>
                <td><strong>Patente:</strong> {{$cotizacion->patente}}</td>
            </tr>
            @endif

            <tr>
                <td colspan="3"><small>Precios sujeto a modificación sin previo aviso</small></td>
            </tr>
        </table>
    </div>
    @yield('content')
    <hr>

    <h3>Observaciones</h3>
    <p>{{$cotizacion->observation}}</p>
</body>

</html>
