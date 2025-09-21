@extends('pdf.cotizaciones.layout')

@section('content')
    <div class="wrapper" style="margin:0;margin-top: -2px; padding-bottom: 300px;">
        {{-- Detalle productos --}}
        <table class="table-productos">
            <tr>
                <th style="width: 65%">Descripción</th>
                <th style="width: 7%">Cant.</th>
                <th style="width: 11%">Precio U.</th>
                <th style="width: 13%">Total</th>
            </tr>

            @foreach ($detail as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['amount'] }}</td>
                    <td>{{ $item['unit_price'] }}</td>
                    <td>{{ $item['total'] }} </td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- Total --}}
@endsection
