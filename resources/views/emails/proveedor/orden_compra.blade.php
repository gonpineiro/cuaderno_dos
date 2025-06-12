<html>

<head>
    <style>
        body {
            font-family: Arial;
            font: small Arial, Helvetica, sans-serif;
            /* text-align: center; */
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

        th {

            font-size: small;
        }

        .td {
            border: 1px solid #dddddd !important;
            text-align: left;
            padding: 8px;

        }

        .wap_note {
            text-align: center
        }

        .links_container {
            padding: 10px 150px;
            display: flex;
            justify-content: space-around;
        }

        .link_icon {
            margin: 0 auto;
        }

        .btn_container {
            text-align: center;
        }

        .text-align-center {
            text-align: center;
        }

        .btn {
            text-decoration: none;
            font-size: 16px;
            padding: 18px;
            background-color: #034579;
            color: white !important;
            font-weight: bold;
            border-radius: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="banner_container">
            <img src="http://www.allenderepuestos.com.ar/assets/email_images/bannerEmail.jpeg" width="550">
        </div>

        <div class="hr"> </div>



        <p><strong>Estimado, tomar nota del siguiente pedido. Por favor confirmar la recepción del mismo y avisar
                cualquier
                novedad al
                respecto:</strong></p>

        <br>

        <table class="table">
            <tr>
                <th>CANTIDAD</th>
                <th>CÓDIGO</th>
                <th>DESCRIPCIÓN</th>
            </tr>
            @foreach ($oc->detail as $item)
            <tr>
                <td class="td">{{$item['amount']}}</td>
                <td class="td">
                    @if(!empty($item['product']['provider_code']))
                    {{ $item['product']['provider_code'] }}
                    @else
                    {{ $item['product']['code'] }}
                    @endif
                </td>

                <td class="td">{{$item['product']['description']}}</td>
            </tr>
            @endforeach

        </table>

        <div class="hr"> </div>

</body>

</html>
