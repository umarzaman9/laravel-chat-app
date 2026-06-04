
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
    $('.user-list').click(function () {

        $('#chatContainer').html('');

        var userId = $(this).attr('data-id')
        receiverId = userId;

        $('.startHead').hide();
        $('.chatSection').show();

        loadOldChats();
    });
});


//save chat
$('#chatForm').submit(function (e) {
    console.log('chat form clicked');
    e.preventDefault();
    var message = $('#message').val();

    $.ajax({
        url: '/save-chat',
        type: 'POST',
        data: {
            senderId: senderId,
            receiverId: receiverId,
            message: message
        },
        success: function (response) {
            if (response.success) {
                $('#message').val('');
                let chat = response.data.message;
                let html = `
                    <div class="currentUser">
                        <h5>`+ chat + `</h5>
                    </div>
                `;
                $('#chatContainer').append(html);
                scrollChat();
            } else {
                alert(response.message);
            }
        }
    });
});

//load chats
function loadOldChats() {
    $.ajax({
        url: '/load-chats',
        type: 'GET',
        data: {
            senderId: senderId,
            receiverId: receiverId
        },
        success: function (response) {
            if (response.success) {
                let chats = response.data;
                let html = '';
                for (let i = 0; i < chats.length; i++) {
                    let addClass = '';
                    if (chats[i].senderId == senderId) {
                        addClass = 'currentUser';
                    } else {
                        addClass = 'distantUser';
                    }
                    html += `
                     <div class="`+ addClass + `">
                        <h5>`+ chats[i] + `</h5>
                    </div>
                    `;
                }
                $('#chatContainer').append(html);
                scrollChat();
            } else {
                alert(response.message);
            }
        }
    });
}

Echo.join('user-status')
    .here((users) => {
        for (let x = 0; x < users.length; x++) {
            if (senderId != users[x]['id']) {
                $('#' + users[x]['id'] + '-status').removeClass('offlineStatus');
                $('#' + users[x]['id'] + '-status').addClass('onlineStatus').text('Online');
            }
        }
    })
    .joining((user) => {
        $('#' + user.id + '-status').removeClass('offlineStatus');
        $('#' + user.id + '-status').addClass('onlineStatus').text('Online');
    })
    .leaving((user) => {
        $('#' + user.id + '-status').addClass('offlineStatus');
        $('#' + user.id + '-status').removeClass('onlineStatus').text('Offline');
    })
    .listen('UserStatusEvent', (e) => {
        //
    })

//scroll div
function scrollChat() {
    $('#chatContainer').animate({
        scrollTop: $('#chatContainer').offset().top + $('#chatContainer')[0].scrollHeight,

    }, 0);
}

Echo.private('broadcast-message')
    .listen('.chatMessage', (data) => {
        console.log(data);

        if (senderId == data.chat.receiverId && receiverId == data.chat.senderId) {
            let html = `
                <div class="distantUser">
                    <h5>`+ data.chat.message + `</h5>
                </div>
        `;
            $('#chatContainer').append(html);
            scrollChat();
        }
    });