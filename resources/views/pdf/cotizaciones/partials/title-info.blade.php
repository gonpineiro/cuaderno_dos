<!-- partials/title-info.blade.php -->
<table style="margin-top:15px;">
    <tr>
        <td style="width: 60%;">
            <h1 style="margin:0;font-size:2rem">COTIZACIÓN</h1>
        </td>
    </tr>
    <tr>
        <td style="width: 40%;">
            <h2 style="color: black; margin:0">
                Nro. de Cotización:
                <span style="font-weight: normal">
                    {{$cotizacion->id}}
                </span>
            </h2>
        </td>
        <td style="width: 40%; text-align:right;">
            <h3 style="color: black; margin:0">
                Fecha:
                <span style="font-weight: normal">
                    {{date("d/m/Y", strtotime($cotizacion->created_at))}}
                </span>
            </h3>
        </td>
    </tr>
</table>
