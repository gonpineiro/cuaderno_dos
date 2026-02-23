{{-- base.blade --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4;
            margin: 0mm;
            /* Espacio para el footer */
            margin-bottom: 35mm;
        }

        .margin-x-container {
            margin-left: 10mm;
            margin-right: 10mm;
        }

        .margin-top-5mm {
            margin-top: 5mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #003368;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-productos-header {
            padding: 10px;
        }

        .table-productos {
            border: 1px solid rgba(0, 0, 0, .125);
            margin-bottom: 16px !important;
        }

        .table-productos tr th {
            background: #003368;
            color: white;
        }

        .table-productos tr,
        .table-productos tr th,
        .table-productos tr td {
            border: 1px solid rgba(0, 0, 0, .125);
            font-size: 0.8rem;
            text-align: left
        }
    </style>
</head>

<body>

    @yield('content')

</body>

</html>
