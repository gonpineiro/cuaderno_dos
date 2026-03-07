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
    @include('pdf.cotizaciones.partials.products-table')
</div>

@include('pdf.partials.footer')


@endsection