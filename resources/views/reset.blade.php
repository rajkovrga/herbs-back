<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikacija naloga</title>

    <style type="text/css">
        *
        {
            padding:0px;
            margin:0px;
            text-align: center;
            background: #5bff86;
            text-decoration: none;
        }

        p
        {
            margin: 39px;
        }

        a
        {
            color:white;
            padding:15px;
            background: #006413;
        }

        a:hover
        {
            background: #2a6440;
            transition:0.5s;
        }
    </style>
</head>
<body>

<h1>Sajt Lekovitih Biljki</h1>

<p>Kliknite na dugme ispod i resetujte vasu nalog.</p>

<a href="{{ $url }} ">Reset lozinke</a>
</body>
</html>
