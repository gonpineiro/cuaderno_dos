@extends('pdf.cotizaciones.layout')

@section('content')
    {{-- Detalle productos --}}
    <div class="wrapper" style="margin:0;margin-top: -2px;">
        <table style="padding: 0; padding-bottom: 300px; table-layout: auto;">
            <tr style="border-bottom: 1px solid #000;">
                <th style="white-space: nowrap;">Cant.</th>
                <th>Descripción</th>
                <th style="white-space: nowrap;">Precio U.</th>
                <th style="white-space: nowrap;">Total</th>
            </tr>

            @foreach ($detail as $item)
                <tr>
                    <td style="white-space: nowrap;">{{ $item['amount'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td style="white-space: nowrap;">{{ $item['unit_price'] }}</td>
                    <td style="white-space: nowrap;">{{ $item['total'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="wrapper flex space-around" style="margin-top: -2px; padding: 5px; text-align: right;padding-right: 20px;">
        IMPORTE TOTAL {{ $is_contado ? 'PRECIO CONTADO' : 'DE LISTA' }}: <span class="total">{{ $total }}</span>
    </div>
@endsection
