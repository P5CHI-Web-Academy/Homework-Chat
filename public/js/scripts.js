'use strict';

/* eslint no-invalid-this: off, require-jsdoc: off */
jQuery.noConflict();
(function ($) {
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
                toastr.success(data.success);
                textarea.val('');
            }).fail(function (xhr) {
                var msg = xhr.responseJSON.error ? xhr.responseJSON.error : 'Message was not sent';
                toastr.error(msg);
                textarea.focus();
            }).always(function () {
                btn.prop('disabled', false);
            });
        });
    });

    var getMessages = function getMessages() {
        var template = $('#ct-message-template');
        var messageContainer = $('#ct-dialog__messages-container');

        $.ajax({
            url: '/messages',
            method: 'GET'
        }).done(function (data) {
            console.log(data);
            $.each(data, function (n, msg) {
                if ($('#ct-message-' + msg.id).length === 0) {
                    var elm = template.clone().show();
                    elm.attr('id', 'ct-message-' + msg.id);
                    elm.find('.ct-media__address').html(msg.message);
                    messageContainer.append(elm);
                }
            });
        }).fail(function (xhr) {
            var msg = xhr.responseJSON.error ? xhr.responseJSON.error : 'Unable to fetch messages';
            toastr.error(msg);
        });
    };

    setInterval(getMessages, 3000);
})(jQuery);