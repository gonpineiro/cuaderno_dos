<html>

<head>
    <style>
        body {
            text-align: center;
        }

        .container {
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
            padding: 20px;
        }

        .center_table {
            width: 100%;
        }

        .banner_container {
            text-align: center
        }

        .hr {
            margin-top: 5px;
            margin-bottom: 5px;
            border-top: 2px solid #eaeaea;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="banner_container">
            <img src="http://cuaderno.allenderepuestos.com.ar/img/bannerEmail.jpeg" width="550">
        </div>

        <div class="hr"> </div>

        @yield('content')
    </div>

</body>

</html>
