/* eslint no-invalid-this: off, require-jsdoc: off */
jQuery.noConflict();
(function($) {
    // apply logic only to chat area
    if (window.location.pathname !== '/') {
        return false;
    }

    let isTyping = false;

    const createChatEntry = function(side, text, time) {
        let elm = $('#ct-message-template' + side).clone().attr('id', null);
        let msgContainer = $('#ct-dialog__messages-container');
        elm.find('.ct-message__text').html(text);
        elm.find('.ct-message__time').html(time);
        msgContainer.append(elm);
        msgContainer.scrollTop(msgContainer[0].scrollHeight);
    };

    const getMessages = function() {
        let receiverId = $('.ct-contact--active').first().data('id');

        $.ajax({
            url: '/messages',
            method: 'GET',
            data: {'receiver': receiverId}
        }).done(function(data) {
            $('#ct-dialog__messages-container').empty();
            $.each(data, function(n, msgObj) {
                let type = msgObj.type === 'user' ? '-my' : '';
                createChatEntry(type, msgObj.message, msgObj.time);
            });
        }).fail(function(xhr) {
            let msg = (xhr.responseJSON && xhr.responseJSON.error) ?
                xhr.responseJSON.error : 'Unable to fetch messages';
            toastr.error(msg);
        });
    };

    $('.ct-contact').click(function(e) {
        let contactName = $(this).find('.ct-contact__user-name').html();

        $('.ct-dialog__interlocutor').show().find('.ct-dialog__interlocutor-name').html(contactName);
        $('#ct-message__form').find('input.ct-dialog__send-bar-input').prop('disabled', false);

        $('.ct-contact--active').removeClass('ct-contact--active');
        $(this).addClass('ct-contact--active');

        let fromId = $(this).data('id');

        $.ajax({
            url: '/set-seen',
            method: 'POST',
            data: {'sender': fromId}
        });

        $(this).find('.ct-contact__user-unread-message-number span').html('0').parent().hide();

        getMessages();
    });

    $('#ct-message__form').submit(function(e) {
        e.preventDefault();

        const $this = $(this);
        const btn = $this.find('button[type=submit]');
        const input = $this.find('input.ct-dialog__send-bar-input');
        let receiverId = $('.ct-contact--active').first().data('id');

        btn.prop('disabled', true);

        $.ajax({
            url: $this.attr('action'),
            method: $this.attr('method'),
            data: {'receiver': receiverId, 'message': input.val()}
        }).done(function(data) {
            // create a new chat entry
            createChatEntry('-my', input.val(), data.time);
            // clear input and notify about this
            input.val('');
            $('#ct-dialog__input').trigger('input');
            // send message to other user's chatbox
            data.type = 'message';
            conn.send(JSON.stringify(data));
        }).fail(function(xhr) {
            let msg = (xhr.responseJSON && xhr.responseJSON.error) ?
                xhr.responseJSON.error : 'Message was not sent';
            toastr.error(msg);
        }).always(function() {
            btn.prop('disabled', false);
        });
    });

    $('#ct-dialog__input').on('input', function() {
        let inputLength = $(this).val().length;

        if (inputLength !== 0 && !isTyping) {
            isTyping = true;
            let msg = {
                sender: $('#ct-me').data('id'),
                type: 'typing',
                typing: isTyping
            };

            conn.send(JSON.stringify(msg));
        } else if (!inputLength && isTyping) {
            isTyping = false;
            let msg = {
                sender: $('#ct-me').data('id'),
                type: 'typing',
                typing: isTyping
            };

            conn.send(JSON.stringify(msg));
        }
    });

    const conn = new WebSocket('ws://' + window.location.hostname + ':8081');

    conn.onmessage = function(evt) {
        let msg = JSON.parse(evt.data);

        switch (msg.type) {
            case 'online':
                let contactStatus = $('#ct-contact-' + msg.id).find('.ct-avatar__user-status').first();
                if (!contactStatus.hasClass('ct-avatar__user-status--online')) {
                    contactStatus.addClass('ct-avatar__user-status--online');
                }
                break;
            case 'message':
                let sender = $('#ct-contact-' + msg.sender);
                if (sender.hasClass('ct-contact--active')) {
                    createChatEntry('', msg.message, msg.time);

                    $.ajax({
                        url: '/set-seen',
                        method: 'POST',
                        data: {'sender': msg.sender}
                    });
                } else {
                    let newMessagesCountEl = sender.find('.ct-contact__user-unread-message-number span');
                    newMessagesCountEl.html(parseInt(newMessagesCountEl.html()) + 1);
                    newMessagesCountEl.parent().show();
                }
                break;
            case 'refreshUsers':
                $('.ct-avatar__user-status--online').removeClass('ct-avatar__user-status--online');
                for (let i = 0; i < msg.users.length; i++) {
                    let contactStatus = $('#ct-contact-' + msg.users[i]).find('.ct-avatar__user-status').first();
                    if (!contactStatus.hasClass('ct-avatar__user-status--online')) {
                        contactStatus.addClass('ct-avatar__user-status--online');
                    }
                }
                break;
            case 'typing':
                let contactSubStatus = $('#ct-contact-' + msg.sender).find('.ct-contact__user-last-message').first();
                contactSubStatus.html(msg.typing ? 'Typing...' : 'Idle');
                break;
        }
    };

    conn.onopen = function(evt) {
        let msg = {
            id: $('#ct-me').data('id'),
            type: 'online'
        };
        conn.send(JSON.stringify(msg));
    };
})(jQuery);
