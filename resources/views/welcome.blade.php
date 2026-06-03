<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>

<body>
    <p>
        <b>Message: </b> <span id="msgData"></span>
    </p>

    <script src="{{ asset('build/assets/app-C7yuJyHJ.js') }}"></script>
    <script>
        Echo.channel('chats').listen('WebSocketEvent', (e) => {
            console.log(e);
            document.getElementById('msgData').innerHTML = e.message;
        })
    </script>
</body>

</html>