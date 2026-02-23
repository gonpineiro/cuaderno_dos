{{-- client-vehicle.blade.php --}}

@php
$tipo_precio = $cotizacion->type_price->value == 'contado' ? 'Contado / debito /
tarjeta 1 pago. IVA INCLUIDO' : 'Lista';

@endphp
<table style="margin-top:15px;">
    <tr>
        <td style="width:60%; vertical-align: top;">
            <h1 style="margin: 0;padding:0;">
                <table style="display:inline-table; vertical-align: middle;width:auto">
                    <tr>
                        <td style="vertical-align: middle; padding-right:6px;">
                            <img src="{{ public_path('assets/images/icons/user_orange.png') }}" height="32">
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size:2rem; font-weight:bold; color:#003368;">
                                Cliente
                            </span>
                        </td>
                    </tr>
                </table>
            </h1>

            <div style="line-height:20px; font-size: 0.9rem;">
                Nombre: {{ $cotizacion->client->name }}<br>
                Teléfono: {{ $cotizacion->client->phone }}<br>
                Dirección: {{ $cotizacion->client->adress }}
            </div>
        </td>

        <td style="width:40%; vertical-align: top;">
            <h1 style="margin: 0;padding:0;">
                <table style="display:inline-table; vertical-align: middle;width:auto">
                    <tr>
                        <td style="vertical-align: middle; padding-right:6px;">
                            <img src="{{ public_path('assets/images/icons/car_orange.png') }}" height="30">
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size:2rem; font-weight:bold; color:#003368;">
                                Vehículo
                            </span>
                        </td>
                    </tr>
                </table>
            </h1>

            <div style="line-height:20px; font-size: 0.9rem;">
                Vehículo: {{ $cotizacion->vehiculo->name }}<br>
                Versión: {{ $cotizacion->version }}<br>
                Patente: {{ $cotizacion->patente }}
            </div>
        </td>
    </tr>
</table>
