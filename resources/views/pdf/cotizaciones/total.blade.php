{{-- cotizacion.blade.php --}}
@extends('pdf.cotizaciones.layouts.base')

@section('content')

<div class="margin-x-container" style="margin-top: 5mm">
    @include('pdf.cotizaciones.partials.header-brands')
</div>

@include('pdf.cotizaciones.partials.header-contact')

<div class="margin-x-container">
    @include('pdf.cotizaciones.partials.title-info')
    @include('pdf.cotizaciones.partials.client-vehicle')
    @include('pdf.cotizaciones.partials.total-products-table')
</div>

@if (optional(optional($cotizacion->client)->condicion_iva)->value === 'resp_incripto')
<div class="margin-x-container" style="margin-top: 8mm; text-align: center; font-size: 0.95rem; font-weight: bold;">
    PRECIOS SIN IVA
</div>
@endif

@include('pdf.partials.footer')


@endsection
