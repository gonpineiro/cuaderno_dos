<html>

<head>
    <style>
        body {
            font-family: Arial;
            font: medium Arial, Helvetica, sans-serif;
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

        @yield('content')

        <div class="hr"> </div>

        <br>
        <div class="wap_note">
            <a href="https://api.whatsapp.com/send/?phone=542995935575&text&type=phone_number&app_absent=0"
                target="_blank">
                +54 299 593-5575
                <br>
                Envianos tus consultas a través de nuestro whatsapp!
            </a>

        </div>

        <br>
        <div class="links_container">
            <a href="http://www.allenderepuestos.com.ar/" class="link_icon">
                <img src="http://www.allenderepuestos.com.ar/assets/email_images/icon_link_black.png" alt="" width="24"
                    height="24">
            </a>
            <a href="http://www.facebook.com/repuestosallende" class="link_icon">
                <img src="http://www.allenderepuestos.com.ar/assets/email_images/icon_fb_black.png" alt="" width="24"
                    height="24">
            </a>
            <a href="https://www.instagram.com/allende_repuestos/" class="link_icon">
                <img src="http://www.allenderepuestos.com.ar/assets/email_images/icon_ins_black.png" alt="" width="24"
                    height="24">
            </a>
            <a href="mailto:contacto@allenderepuestos.com.ar" class="link_icon">
                <img src="http://www.allenderepuestos.com.ar/assets/email_images/icon_email_black.png" alt="" width="24"
                    height="24">
            </a>
        </div>
    </div>

</body>

</html>
