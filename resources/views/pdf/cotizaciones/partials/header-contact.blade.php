{{-- header-contact.blade.php --}}

@php
$cellStyle = 'width:25%;padding:6px;vertical-align: middle;text-align:center;';
$itemStyle = 'line-height:14px;text-align:center';
@endphp

<div style="background:#efefef; margin-top:8px;">
    <table style="table-layout: fixed;margin-left:3mm;">
        <tr>
            <td style="{{ $cellStyle }}">
                <span style="{{ $itemStyle }}">
                    <img src="{{ public_path('assets/images/ig.png') }}" height="14"
                        style="vertical-align: baseline; margin-right:4px;">
                    @allende_repuestos
                </span>
            </td>

            <td style="{{ $cellStyle }}">
                <span style="{{ $itemStyle }}">
                    <img src="{{ public_path('assets/images/wapp.png') }}" height="14"
                        style="vertical-align: baseline; margin-right:4px;">
                    2995935575
                </span>
            </td>

            <td style="{{ $cellStyle }}">
                <span style="{{ $itemStyle }}">
                    <img src="{{ public_path('assets/images/web.png') }}" height="14"
                        style="vertical-align: baseline; margin-right:4px;">
                    allenderepuestos.com.ar
                </span>
            </td>

            <td style="{{ $cellStyle }}">
                <span style="{{ $itemStyle }}">
                    <img src="{{ public_path('assets/images/tel.png') }}" height="14"
                        style="vertical-align: baseline; margin-right:4px;">
                    0299 4781525
                </span>
            </td>

        </tr>
    </table>
</div>
