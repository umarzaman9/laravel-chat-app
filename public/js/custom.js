
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


// ── NOTIFICATIONS ─────────────────────────────────────────────────

// Poll every 10 seconds. Works alongside your existing Echo/WebSocket
// setup — they don't interfere with each other.
fetchNotifications();
setInterval(fetchNotifications, 10000);

function fetchNotifications() {
    $.ajax({
        url: '/notifications',
        type: 'GET',
        success: function (response) {
            if (!response.success) return;

            var count = response.count;
            var $badge = $('#notifBadge');
            var $list = $('#notifList');

            // Badge: show only when there are unread messages
            if (count > 0) {
                $badge.text(count).show();
            } else {
                $badge.hide();
            }

            // Notification list
            $list.empty();
            if (response.data.length === 0) {
                $list.append(
                    '<li class="list-group-item text-muted" style="font-size:13px;">No new notifications</li>'
                );
                return;
            }

            $.each(response.data, function (i, notif) {
                // notif.data is the JSON object you returned from toDatabase()
                // so notif.data.message and notif.data.senderId are available
                var $li = $('<li>')
                    .addClass('list-group-item list-group-item-action')
                    .css('cursor', 'pointer')
                    .html(
                        '<small class="text-muted">User #' + notif.data.senderId + ' says:</small><br>' +
                        '<span>' + notif.data.message + '</span>'
                    )
                    .on('click', function () {
                        markOneRead(notif.id);
                    });
                $list.append($li);
            });
        }
    });
}

function toggleNotifPanel() {
    $('#notifPanel').toggle();
}

function markOneRead(id) {
    $.ajax({
        url: '/notifications/' + id + '/read',
        type: 'POST',
        success: function () {
            fetchNotifications(); // refresh badge + list
        }
    });
}

function markAllRead() {
    $.ajax({
        url: '/notifications/mark-all-read',
        type: 'POST',
        success: function () {
            fetchNotifications();
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