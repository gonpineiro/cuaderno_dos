@extends('emails.layout')

@section('content')
<p>
    <strong>Estimado, tomar nota del siguiente pedido. Por favor confirmar la recepción del mismo y avisar cualquier novedad al respecto:</strong>
</p>

<br>

<table class="table">
    <thead>
        <tr>
            <th>Cantidad</th>
            <th>Código</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($oc->detail as $item)
        <tr>
            <td class="td">{{ $item['amount'] }}</td>
            <td class="td">
                {{ !empty($item['product']['provider_code']) ? $item['product']['provider_code'] : $item['product']['code'] }}
            </td>
            <td class="td">{{ $item['product']['description'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p>
    Saludos,
</p>
@endsection
