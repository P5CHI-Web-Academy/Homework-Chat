// /* eslint no-invalid-this: off, require-jsdoc: off */
// jQuery.noConflict();
// (function($) {
//     $(function() {
//         $('#ct-message__form').submit(function(e) {
//             e.preventDefault();
//
//             const btn = $(this).find('button[type=submit]');
//             btn.prop('disabled', true);
//
//             const input = $(this).find('.ct-message__form__input');
//
//             const $this = $(this);
//             $.ajax({
//                 url: $this.attr('action'),
//                 method: $this.attr('method'),
//                 data: $this.serialize()
//             }).done(function(data) {
//                 toastr.success(data.success);
//                 input.val('');
//             }).fail(function(xhr) {
//                 let msg = xhr.responseJSON.error ?
//                     xhr.responseJSON.error : 'Message was not sent';
//                 toastr.error(msg);
//             }).always(function() {
//                 btn.prop('disabled', false);
//             });
//         });
//     });
//
//     let getMessages = function(id) {
//         let template = $('#ct-message-template');
//         let messageContainer = $('#ct-dialog__messages-container').html('');
//         let loggedUser = $('meta[name=loggedUserId]').attr("content");
//
//         $.ajax({
//             url: '/messages',
//             method: 'GET',
//             data: { 'id': id, 'loggedUser': loggedUser }
//         }).done(function(data) {
//             console.log(data);
//             $.each(data, function(n, msg) {
//                 if ($('#ct-message-' + msg.id).length == 0) {
//                     let elm = template.clone().show();
//                     if (loggedUser == msg.sender) {
//                         elm.addClass('ct-message--my');
//                     }
//                     elm.attr('id', 'ct-message-' + msg.id);
//                     elm.find('.ct-media__address').html(msg.message);
//                     messageContainer.append(elm);
//                 }
//             });
//         }).fail(function(xhr) {
//             let msg = xhr.responseJSON.error ?
//                 xhr.responseJSON.error : 'Unable to fetch messages';
//             toastr.error(msg);
//         });
//     };
//
//
//     setInterval(function () {
//         getMessages(1);
//     }, 3000);
//
//
// })(jQuery);


Vue.component('message', {
    props: ['message'],

    template: `
            <div id="ct-message-template" class="ct-message">

            <div class="ct-message__container">
                <div class="ct-media">
                    <div class="ct-media__info-container">
                        <div class="ct-media__address">{{ message.message }}</div>
                    </div>
                </div>
            </div>

            <div class="ct-message__user">
                <div class="ct-avatar ct-avatar--small">
                    <img src="" alt="" class="ct-avatar__image">
                </div>
            </div>
        </div>
        `
})
new Vue({
    el: '#root',

    data: {
        messages: [],
        users: [],

        message: '',
        user: parseInt(document.querySelector("meta[name=loggedUserId]").content),

        chat: false,
        chatUser: 0,
        chatUserName: '',

        uploading: false,
    },

    beforeMount() {
        $this = this;
        setInterval(function () {
            $this.fetchMessages($this.chatUser);
        }, 3000);
    },

    mounted() {
        axios.get('/users',).then(response => this.users = response.data);
    },

    methods: {
        fetchMessages(chatUser) {
            this.chatUser = chatUser;

            $this = this;

            axios.get('/messages', {
                params: {
                    chatUser: chatUser,
                }
            }).then(function (response) {
                if ($this.messages != response.data) {
                    $this.messages = response.data;
                }
            });
        },

        saveMessage() {
            var formData = new FormData();
            formData.append('sender', this.user);
            formData.append('receiver', this.chatUser);
            formData.append('message', this.message);

            axios.post('/store', formData);

            $this.message = ''
        },

        saveAvatar() {
            var formData = new FormData();
            var fileField = document.querySelector("input[type='file']");

            formData.append('avatar', fileField.files[0]);
            formData.append('user', this.user);

            fetch('/avatar', {
                method: 'POST',
                body: formData
            });

            // location.reload();
        }
    }
})