@extends('pdf.cotizaciones.layout')

@section('content')
    <div class="wrapper" style="margin:0;margin-top: -2px;padding-bottom: 300px;">
        {{-- Detalle productos --}}
        <table class="table-productos">
            <tr>
                <th style="width: 13%">Código</th>
                <th style="width: 12%">Cod. Prov.</th>
                <th style="width: 10%">C. Fabrica</th>
                <th>Descripción</th>
                <th style="width: 5%">Cant.</th>
                <th style="width: 11%">Precio U.</th>
                <th style="width: 13%">Total</th>
                <th style="width: 5%">Ubicación</th>
            </tr>

            @foreach ($detail as $item)
                <tr>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['provider_code'] }}</td>
                    <td>{{ $item['factory_code'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['amount'] }}</td>
                    <td>{{ $item['unit_price'] }}</td>
                    <td>{{ $item['total'] }} </td>
                    <td>{{ $item['ubication'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
{{-- Total --}}
{{-- <p class="w-100 importe">
    IMPORTE TOTAL {{$is_contado ? 'IMPORTE TOTAL PRECIO CONTADO' : ''}}: <span class="total">{{$total}}</span>
</p>
--}}
