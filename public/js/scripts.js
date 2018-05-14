'use strict';

/* eslint no-invalid-this: off, require-jsdoc: off */
jQuery.noConflict();
(function ($) {
    // message = {'id': '...', 'message': '...', 'createdAt': '...'}
    function appendMessage(message) {
        if (message.userId == $('input[name="interlocutorId"]').val()) {
            var template = $('#message_template');
        } else {
            var template = $('#message_template_my');
        }

        var messageContainer = $('#ct-dialog__messages-container');
        var elm = template.clone().show();
        elm.attr('id', 'ct-message-' + message.id);
        elm.find('.ct-message__text').html(message.message);
        elm.find('.ct-message__time').html(message.createdAt);
        messageContainer.append(elm);
    }

    function updateScroll(){
        $("#ct-dialog__messages-container")
            .animate({
                scrollTop: $("#ct-dialog__messages-container").prop("scrollHeight") },
                700,
                "linear"
            );
    }

    $(function () {
        $('#ct-message__form').submit(function (e) {
            e.preventDefault();

            var btn = $(this).find('button[type=submit]');
            btn.prop('disabled', true);

            var textarea = $(this).find('textarea');

            var $this = $(this);
            $.ajax({
                url: $this.attr('action'),
                method: $this.attr('method'),
                data: $this.serialize()
            }).done(function (data) {
                if (data.success) {
                    toastr.success(data.success);
                    appendMessage(data);

                    textarea.val('');
                } else if (data.error) {
                    var msg = data.error ? data.error : 'Message was not sent';
                    toastr.error(msg);
                }

                updateScroll();
                textarea.focus();
            }).fail(function (xhr) {
                var msg = xhr.responseJSON.error ? xhr.responseJSON.error : 'Message was not sent';
                toastr.error(msg);
                textarea.focus();
            }).always(function () {
                btn.prop('disabled', false);
            });
        });
    });

    var getMessages = function() {
        var token = $('input[name="token"]').val();
        var interlocutorId = $('input[name="interlocutorId"]').val();

        $.ajax({
            url: '/messages/' + token + '/' + interlocutorId,
            method: 'GET'
        }).done(function (data) {
            var new_messages = 0; // counter
            $.each(data, function (n, msg) {
                if ($('#ct-message-' + msg.id).length === 0) {
                    appendMessage(msg);
                    new_messages ++;
                }
            });
            if (new_messages) { // If at least one new message appended, update scroll
                updateScroll();
            }

        }).fail(function (xhr) {
            var msg = xhr.responseJSON.error ? xhr.responseJSON.error : 'Unable to fetch messages';
            toastr.error(msg);
        });
    };

    var getUnreadMessagesCount = function () {
        var token = $('input[name="token"]').val();
        $.ajax({
            url: '/unreadMessagesCount/' + token,
            method: 'GET'
        }).done(function (data) {
            $.each(data, function (n, item) {
                var container = $('#unread_message_number_container_' + item.userId);
                if (container.length !== 0) {
                    if (item.msgCount) {
                        container.find('span').html(item.msgCount);
                        container.show();
                    } else {
                        container.hide();
                    }
                }
            });
            if (new_messages) { // If at least one new message appended, update scroll
                updateScroll();
            }

        }).fail(function (xhr) {
            var msg = xhr.responseJSON.error ? xhr.responseJSON.error : 'Unable to fetch messages';
            toastr.error(msg);
        });
    }

    if ($('#ct-message__send-div').length) {
        getMessages();
        setTimeout(updateScroll, 300);
        setInterval(getMessages, 3000);
    }
    getUnreadMessagesCount();
    setInterval(getUnreadMessagesCount, 7000);
})(jQuery);
