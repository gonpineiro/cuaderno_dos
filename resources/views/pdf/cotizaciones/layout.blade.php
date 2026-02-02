<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        html,
        body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            height: 297mm;
            border-collapse: collapse;
        }

        .page-content {
            vertical-align: top;
        }

        .header-logos {
            width: 100%;
            padding: 10px 50px;
            border-collapse: collapse;
        }

        .header-logos td {
            vertical-align: middle;
        }

        .logo-main img {
            width: 260px;
        }

        .logos-right {
            text-align: right;
            white-space: nowrap;
        }

        .logos-right img {
            vertical-align: middle;
            margin-left: 15px;
        }


        .contact-bar {
            background: #efefef;
            padding: 7px 15px;
            width: 100%;
            font-size: 0.9rem;
        }

        .contact-bar td {
            white-space: nowrap;
        }

        .contact-bar img {
            width: 16px;
            margin-right: 4px;
            vertical-align: middle;
        }

        .cotizacion-title {
            margin-top: 25px;
            padding-left: 50px;
        }

        .cotizacion-title h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .cotizacion-meta {
            font-size: 1.2rem;
            padding: 0 50px;
        }

        .bloque-datos {
            margin-top: 25px;
            padding: 0 50px;
        }

        .bloque-titulo {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .linea-dato {
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
            padding-bottom: 2px;
            font-size: 1.2rem;
        }

        .page-footer {
            height: 40mm;
            background: #efefef;
        }

        .footer-inner {
            width: 100%;
            height: 100%;
            padding: 0 50px;
            border-collapse: collapse;
        }

        .footer-inner td {
            vertical-align: middle;
            font-size: 1rem;
        }

        .footer-inner img {
            width: 45px;
            margin-right: 8px;
        }

        .footer-left {
            white-space: nowrap;
        }

        .footer-right {
            text-align: right;
            white-space: nowrap;
        }

        .table-wrap {
            padding: 0 10px;
            margin-top: 50px;
        }

        .table-productos {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 15px 0;
            font-size: 0.8rem;
        }

        .table-productos th {
            border: 1px solid #15375a;
        }

        .table-productos thead th,
        .table-productos .table-header th {
            font-size: 1.2rem;
            background-color: #15375a;
            color: #fff;
            padding: 8px 6px;
        }

        .table-productos th,
        .table-productos td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .table-productos th {
            font-weight: 700;
            /*  border-bottom: 1px solid #000; */
        }

        .table-productos td {
            border-bottom: 1px solid #bbb;
        }

        .table-productos th:nth-child(2),
        .table-productos td:nth-child(2) {
            text-align: center;
            width: 7%;
        }

        .table-productos th:nth-child(3),
        .table-productos td:nth-child(3),
        .table-productos th:nth-child(4),
        .table-productos td:nth-child(4) {
            text-align: right;
            width: 13%;
        }

        .table-productos th:first-child,
        .table-productos td:first-child {
            width: 60%;
            word-wrap: break-word;
        }

        .table-totales {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .table-totales td {
            padding: 6px;
            border: 1px solid #000;
        }

        .td-spacer {
            width: 65%;
            border: none !important;
            padding: 0 !important;
        }

        .label-total {
            text-align: right;
            font-weight: 600;
        }

        .value-total {
            text-align: right;
            font-weight: 700;
        }




        .importe {
            margin: 6px 50px;
            font-size: 1.1rem;
            text-align: right;
        }

        .importe .total {
            font-weight: 700;
            margin-left: 10px;
        }

        hr {
            margin: 20px 50px 10px 50px;
            border: none;
            border-top: 2px solid #000;
        }

        .observaciones {
            margin-left: 30px;
        }
    </style>
</head>

<body>
    <table class="page">
        <tr class="page-content">
            <td>

                @if ($type != 'interno')
                <table class="header-logos">
                    <tr>
                        <td class="logo-main">
                            <img src="{{ public_path('assets/images/20260201_logo.png') }}">
                        </td>
                        <td class="logos-right">
                            <img src="{{ public_path('assets/images/20260201_fiat_logo.png') }}" style="width:55px">
                            <img src="{{ public_path('assets/images/peugeot.jpg') }}" style="width:75px">
                            <img src="{{ public_path('assets/images/20260201_renault_logo.png') }}" style="width:85px">
                        </td>
                    </tr>
                </table>


                <table class="contact-bar">
                    <tr>
                        <td>
                            <span style="display:inline-block; vertical-align:middle;">
                                <img src="{{ public_path('assets/images/ig.png') }}">
                            </span>
                            <span style="vertical-align:middle;">
                                {{"@allende_repuestos"}}
                            </span>
                        </td>
                        <td><img src="{{ public_path('assets/images/wapp.png') }}"> 2995935575</td>
                        <td><img src="{{ public_path('assets/images/fb.png') }}"> /repuestosallende</td>
                        <td><img src="{{ public_path('assets/images/web.png') }}"> www.allenderepuestos.com</td>
                        <td><img src="{{ public_path('assets/images/tel.png') }}"> 0299 4781525</td>
                    </tr>
                </table>
                @endif

                <table class="cotizacion-title" width="100%">
                    <tr>
                        <td>
                            <h1>COTIZACIÓN</h1>
                        </td>
                    </tr>
                </table>

                <table class="cotizacion-meta" width="100%">
                    <tr>
                        <td>Nro. de Cotización {{ $cotizacion->id }}</td>
                        <td class="text-end">Fecha {{ date('d/m/Y', strtotime($cotizacion->created_at)) }}</td>
                    </tr>
                </table>

                <table class="bloque-datos" width="100%">
                    <tr>
                        <td width="50%" style="padding-right:20px; vertical-align:top;">
                            <div class="bloque-titulo">Cliente</div>
                            <div class="linea-dato">Nombre: {{ $cotizacion->client->name ?? '' }}</div>
                            <div class="linea-dato">Telefono: {{ $cotizacion->client->phone ?? '' }}</div>
                        </td>
                        <td width="50%" style="padding-left:20px; vertical-align:top;">
                            <div class="bloque-titulo">Vehículo</div>
                            <div class="linea-dato">Vehículo: {{ $cotizacion->vehiculo->name ?? '' }}</div>
                            <div class="linea-dato">Patente: {{ $cotizacion->patente ?? '' }}</div>
                            <div class="linea-dato">Versión: {{ $cotizacion->version ?? '' }}</div>
                        </td>
                    </tr>
                </table>

                @yield('content')

                <div class="observaciones">

                    <h3>Observaciones</h3>
                    <p>{{$cotizacion->observation}}</p>
                </div>

            </td>
        </tr>

        {{-- <tr class="page-footer">
            <td>
                <table class="footer-inner">
                    <tr>
                        <td class="footer-left">
                            <img src="{{ public_path('assets/images/logoallende.png') }}">
                            La mejor parte es <strong>Confianza</strong>
                        </td>
                        <td class="footer-right">
                            25 de Mayo 373 - Cipolletti
                        </td>
                    </tr>
                </table>
            </td>
        </tr> --}}
    </table>
</body>

</html>