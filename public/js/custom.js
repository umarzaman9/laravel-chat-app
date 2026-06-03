$(document).ready(function () {
    $('.user-list').click(function () {
        $('.startHead').hide();
        $('.chatSection').show();
    });
});

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