<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Static Chat App</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>

<body>
    <div id="startChat">
        <form id="saveName" action="">
            <input type="text" name="name" id="name" placeholder="Enter Name..." required>
            <input type="submit" name="" id="" value="Let's Chat">
        </form>
    </div>
    <div id="chatPart">
        <form id="chatForm">
            @csrf
            <input type="hidden" name="username" id="username">
            <input type="text" name="message" id="message" placeholder="Enter Message...">
            <input type="submit" name="" id="" value="Send">
        </form>
        <div id="chatContainer">

        </div>
    </div>

    <script src="{{ asset('build/assets/app-C7yuJyHJ.js') }}"></script>
    <script>
        window.$ = window.jQuery;
        window.jQuery = window.jQuery;
    </script>

    <script>
        $('#chatPart').hide();
        $('#startChat').submit(function(e){
            e.preventDefault();
            $('#username').val($('#name').val());
            $('#startChat').hide();
            $('#chatPart').show();
        });

        $('#chatForm').submit(function(e){
            e.preventDefault();
           var formData = $(this).serialize();
           $.ajax({
            url: "{{ route('broadcastMsg') }}",
            type: 'POST',
            data: formData,
           });
           $('#message').val('');
        });

        Echo.channel('chatting').listen('MessageEvent', (e) =>{
             let html = `<br>
             <b>`+e.username+`: </b>
             <span>`+e.message+`</span>
             `;
             $('#chatContainer').append(html);
        });
    </script>
</body>

</html>