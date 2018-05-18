Vue.component('message', {
    props: ['message', 'chatUser', 'user'],

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
                    
                    <img v-if="message.sender == chatUser.id" :src="chatUser.avatar" class="ct-avatar__image">
                    <img v-if="message.sender == user.id" :src="user.avatar" class="ct-avatar__image">
                    
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

        user: {},
        chatUser: {},

        chat: false,
        uploading: false,
    },

    beforeMount() {
        setInterval(function () {
            $this.fetchMessages(1);
        }, 2000);
    },

    mounted() {
        axios.get('/users').then(response => this.users = response.data);
        axios.get('/user').then(response => this.user = response.data);
    },

    methods: {

        setChatUser(index) {
            this.chatUser = this.users[index];
        },

        fetchMessages() {

            $this = this;

            axios.get('/messages', {
                params: {
                    chatUser: $this.chatUser.id,
                }
            }).then(function (response) {
                if ($this.messages != response.data) {
                    $this.messages = response.data;
                }
            });
        },

        saveMessage() {
            var formData = new FormData();
            formData.append('sender', this.user.id);
            formData.append('receiver', this.chatUser.id);
            formData.append('message', this.message);

            axios.post('/store', formData);

            $this.message = ''
        },

        saveAvatar() {
            var formData = new FormData();
            var fileField = document.querySelector("input[type='file']");

            formData.append('avatar', fileField.files[0]);
            formData.append('user', this.user.id);

            fetch('/avatar', {
                method: 'POST',
                body: formData
            });

            this.uploading = false;

            setTimeout(function(){ location.reload() }, 500);
        }
    }
})