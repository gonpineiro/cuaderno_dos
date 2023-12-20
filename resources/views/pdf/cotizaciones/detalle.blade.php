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

    .importe {
        text-decoration: underline;
        font-size: 1.4rem;
    }

    .total {
        font-weight: 500;
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
                <td class="bold">Tipo Precio:</td>
                <td>{{$cotizacion->type_price->description}}</td>
            </tr>
        </table>
        <hr>
        <h3>Observaciones</h3>
        <p>{{$cotizacion->observation}}</p>
    </div>

    <hr>
    {{-- Detalle productos --}}
    <table class="table-productos">
        <tr>
            @if ($type === 'interno')
            <th>Código</th>
            @else
            <th style="width: 50%">Descripción</th>
            @endif
            <th>Cant.</th>
            <th>Precio U.</th>
            <th>Total</th>
        </tr>


        @foreach ($detail as $item)
        <tr>
            @if ($type === 'interno')
            <td>{{$item->product->code}}</td>
            @else
            <td>{{$item->product->description}}</td>
            @endif

            <td>{{$item->amount}}</td>
            <td>$ {{ number_format(
                $precioContado ?
                round($item->unit_price * $contado_deb->coeficiente * $contado_deb->value) :
                round($item->unit_price),
                2, ',', '.') }}
            </td>
            <td>$ {{ number_format(
                $precioContado ?
                round($item->unit_price * $item->amount * $contado_deb->coeficiente * $contado_deb->value) :
                round($item->unit_price * $item->amount),
                2, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Total --}}
    @if ($type == 'total' && isset($total) && $total)
    <hr>
    <p class="w-100 importe">
        IMPORTE TOTAL: <span class="total">$ {{number_format($total, 2, ',', '.')}}</span>
    </p>
    @endif
    @if ($type == 'total' && isset($precioContado) && $precioContado)
    <p class="w-100 importe">
        IMPORTE TOTAL PRECIO CONTADO: <span class="total">$ {{number_format($precioContado, 2, ',', '.')}}</span>
    </p>
    @endif

    @if (isset($coefs) && $coefs)
    <hr>
    <h2>Tipos de financiación</h2>
    <table class="table-productos">
        <tr>
            @if ($type === 'interno')
            <th>CANTIDAD CUOTAS</th>
            <th>PRECIO</th>
            <th>VALOR CUOTA</th>
            @endif
        </tr>

        @foreach ($coefs as $coef)
        <tr>
            <td>{{ $coef['description']}}</td>
            <td>{{ $coef['price']}}</td>
            <td>{{ $coef['valor_cuota']}}</td>
        </tr>
        @endforeach
    </table>
    @endif
</body>

</html>
