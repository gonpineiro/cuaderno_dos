<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
</head>


<style>
    * {
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    html {
        margin: 25px;
        padding: 25px;
    }

    body {
        /* width: 21cm; */
        /* min-height: 27cm; */
        /* max-height: 29.7cm; */
        font-size: 13px;
        margin: 0;
        padding: 0;
    }

    .wrapper {
        border: 1.5px solid #333;
        /* padding: 5px; */
    }

    .flex-column {
        display: flex;
        flex-direction: column;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .bold {
        font-weight: bold;
    }

    .inline-block {
        display: inline-block;
    }

    .flex {
        display: flex;
        flex-wrap: nowrap;
        align-items: flex-start;
    }

    .no-margin {
        margin: 0;
    }

    .relative {
        position: relative;
    }

    .floating-mid {
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: 75px;
        position: absolute;
        top: 1px;
        background: #fff;
    }

    .space-around {
        justify-content: space-around;
    }

    .space-between {
        justify-content: space-between;
    }

    .w50 {
        width: 50%;
        box-sizing: border-box;
    }

    th {
        /* border: 1px solid #000; */
        /* background: #ccc; */
        padding: 5px;
    }

    td {
        padding: 5px;
        font-weight: normal;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    table {
        border-collapse: collapse;
        width: 100%;

        margin: 0;
    }

    .text-20 {
        font-size: 20px;
    }

    .qr-container img {
        max-width: 100%;
    }

    .small {
        font-size: 9px;
    }
</style>

<body>
    <div class="relative">
        {{-- @if ($type != 'interno') --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; position: relative;">
            <tr>
                <!-- Columna izquierda -->
                <td style="width: 50%; vertical-align: top; padding-right: 10px; border: 1.5px solid #333;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding-bottom: 10px;">
                                <img src="{{ public_path('assets/images/logoallende_cortada.png') }}"
                                    style="width: 90%;">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <img src="{{ public_path('assets/images/fiat.png') }}" style="width: 20%;">
                                <img src="{{ public_path('assets/images/peugeot_old.png') }}"
                                    style="width: 20%; margin: 0 20px;">
                                <img src="{{ public_path('assets/images/renault_logo.png') }}" style="width: 15%;">
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 13px; line-height: 1.5;">
                                <b>25 de Mayo 373 - Telefax 0299 4781525 - 4781433</b>
                                <br>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="border-collapse: collapse; margin-top: 5px;">
                                    <tr>
                                        <!-- Columna izquierda: dirección + email -->
                                        <td style="vertical-align: top; width: 50%;">
                                            <b>8324 Cipolletti - Río Negro</b>
                                            <br>
                                            <b>contacto@allenderepuestos.com.ar</b>
                                        </td>

                                        <!-- Columna derecha: WhatsApp + Facebook -->
                                        <td style="vertical-align: top; width: 50%;">
                                            <img src="{{ public_path('assets/images/wapp.png') }}"
                                                style="width: 15px; vertical-align: middle;">
                                            <b>2995935575</b>
                                            <br>
                                            <img src="{{ public_path('assets/images/fb.png') }}"
                                                style="width: 15px; vertical-align: middle;">
                                            <b>/repuestosallende</b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    </table>
                </td>

                <!-- Columna central -->
                <td style="width: 50%; vertical-align: top; padding-left: 10px; border: 1.5px solid #333;">
                    <h3 class="text-center" style="text-align: center; font-size: 24px; margin-bottom: 35px;">
                        PRESUPUESTO
                    </h3>
                    <p style="font-size: 18px; line-height: 1.5; margin: 0;margin-left: 5px;">
                        <b>N°: {{ $cotizacion->id }}</b>
                        <br><b>Fecha:</b> {{ date('d/m/Y', strtotime($cotizacion->created_at)) }}
                    </p>
                    <p style="font-size: 12px; line-height: 1.5; margin: 0;margin-left: 5px; margin-top: 20px;">
                        <b>IVA RESPONSABLE INSCRIPTO / CUIT: 20-14159053-8</b>
                        <br><b>ING. BRUTOS: 210-9170-6 / INICIO DE ACT.: Diciembre 1994</b>
                        <br><span>www.allenderepuestos.com.ar</span>
                    </p>
                </td>
            </tr>
        </table>

        <!-- DIV flotante sobre las dos columnas -->
        {{-- <div style="position: relative; margin-top: 5px; text-align: center; z-index: 10;"> --}}
        <table
            style="position: absolute; width:10%; margin: 0 auto;top: 0; border: 1.5px solid #333; background: #fff;">
            <tr>
                <td style="padding: 3px; text-align: center;">
                    <span class="bold" style="font-size: 25px; margin: 0;padding: 0; font-weight:bolder;">X</span>
                    <h5 style="font-size: 8px; margin: 0;">
                        <small>DOC. NO VÁLIDO COMO FACTURA</small>
                    </h5>
                </td>
            </tr>
        </table>
        {{-- </div> --}}
    </div>

    <div class="wrapper" style="margin-top: -2px;font-size: 12px;padding: 5px">
        <div class="flex" style="margin-bottom: 5px;">
            <span style="width:30%"><b>Cliente:</b>{{ $cotizacion->client->name }}</span>
        </div>

        <div class="flex" style="flex-wrap: nowrap;margin-bottom: 5px;">
            <span style="width:30%"><b>Direccion:</b>{{ $cotizacion->client->adress ?? '-' }}</span>
        </div>
        <div class="flex" style=";">
            <span style=" padding-right: 18px;"><b style="font-weight: bolder;">IVA:</b>Responsable Inscripto</span>
            <span style="padding-right: 18px;"><b style="font-weight: bolder;">CUIT:</b>2222222222</span>
            <span>
                <b style="font-weight: bolder;">Condición de venta:</b>
                {{ $cotizacion->type_price->value == 'contado' ? 'Contado / debito / tarjeta 1 pago. IVA INCLUIDO' : 'Lista' }}
            </span>
        </div>
    </div>

    <div class="wrapper flex space-around" style="margin-top: -2px;padding: 5px;">
    </div>

    {{-- <div class="images">
            <img src="{{ public_path('assets/images/logoallende.png') }}" style="width: 40%; margin-right: 50px">
            <img src="{{ public_path('assets/images/fiat.png') }}" style="width: 8%; margin-right: 20px">
            <img src="{{ public_path('assets/images/peugeot.png') }}" style="width: 13%; margin-right: 20px">
            <img src="{{ public_path('assets/images/renault.png') }}" style="width: 15%; margin-right: 20px">
        </div>
        <hr>
        <table>
            <tr>
                <td style="vertical-align: middle;" colspan="2">
                    <img src="{{ public_path('assets/images/map.jpg') }}" style="width: 25px"> <span
                        style="vertical-align: middle;">25 de Mayo 373
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
                        style="width: 25px"> <span style="vertical-align: middle;"> {{ '@allende_repuestos' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle;" colspan="2"><img
                        src="{{ public_path('assets/images/mail.png') }}" style="width: 25px"> <span
                        style="vertical-align: middle;">contacto@allenderepuestos.com.ar</span>
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
                    <h2>COTIZACION N°: {{ $cotizacion->id }}</h2>
                </td>
            </tr>
            <hr>
            <tr>
                <td>
                    <strong>Fecha:</strong> {{ date('d/m/Y', strtotime($cotizacion->created_at)) }}
                </td>
                <td>
                    <strong>Vehículo: </strong>{{ $cotizacion->vehiculo->name }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Cliente:</strong> {{ $cotizacion->client->name }}
                </td>
                <td>
                    <strong>Condición Venta:</strong>
                    {{ $cotizacion->type_price->value == 'contado'
                        ? 'Contado / debito                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              /
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        tarjeta 1 pago. IVA INCLUIDO'
                        : 'Lista' }}
                </td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> {{ $cotizacion->contacto }}</td>
            </tr>
            @if (!!$cotizacion->client->is_insurance)
                <tr>
                    <td><strong>Version:</strong> {{ $cotizacion->version }}</td>
                    <td><strong>Patente:</strong> {{ $cotizacion->patente }}</td>
                </tr>
            @endif

            <tr>
                <td colspan="3"><small>Precios sujeto a modificación sin previo aviso</small></td>
            </tr>
        </table>
    </div> --}}

    @yield('content')

    <div class="wrapper" style="margin-top: -2px; font-size: 12px; padding: 10px;">
        <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="width: 1%; white-space: nowrap;">
                    <b
                        style="font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">Observaciones</b>
                </td>
                <td>
                    {{ $cotizacion->observation }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    Vehículo:{{ $cotizacion->vehiculo->name ?? '-' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>Dominio:{{ $cotizacion->patente ?? '-' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    PRECIOS SUJETOS A MODIFICACIÓN SIN PREVIO AVISO.
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    PLAZO DE ENTREGA: 15 DÍAS.
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    PRECIOS CON IVA INCLUIDO.
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    ENTREGA DE MATERIALES SUJETA A DISPONIBILIDAD.
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
