<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="loggedUserId" content="<?php echo $user->getId(); ?>">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="vendor.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade
    your browser</a> to improve your experience and security.</p>
<![endif]-->

<div class="ct-container" id="root">
    <div class="ct-contacts">
        <div class="ct-me">
            <div class="ct-avatar ct-avatar--large">
                <img :src="user.avatar" alt="" class="ct-avatar__image" @click="uploading = true">
            </div>
            <div class="ct-me__username">
                {{ user.name }}
            </div>
            <div class="ct-me__email">
                {{ user.email }}
            </div>

            <form action="/logout" method="post">
                <button type="submit" class="btn">logout</button>
            </form>

            <form method="post" action="/avatar" enctype="multipart/form-data" @submit.prevent="saveAvatar" v-if="uploading">
                <input type="file" name="avatar">
                <button>upload</button>
                <button @click="uploading = false">cancel</button>
            </form>


        </div>

        <div class="ct-contact-container" v-for="(user, index) in users">

            <div class="ct-contact ct-contact--hover" @click="fetchMessages(user.id); chat = true; setChatUser(index)">
                <div class="ct-contact__avatar-container">
                    <div class="ct-avatar ct-avatar--large">
                        <img :src="user.avatar" alt="" class="ct-avatar__image">
                    </div>
                </div>
                <div class="ct-contact__user-info-container" style="display: flex; align-items: center;">
                    <div class="ct-contact__user-name">
                        {{ user.name }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="ct-dialog" v-if="chat">
        <div class="ct-dialog__top-bar">
            <div class="ct-dialog__interlocutor">
                <div class="ct-avatar">
                    <img :src="chatUser.avatar" alt="" class="ct-avatar__image">
                </div>
                <div class="ct-dialog__interlocutor-name">
                    {{ chatUser.name }}
                </div>
            </div>
        </div>

        <div id="ct-dialog__messages-container" class="ct-dialog__messages-container">

            <message v-for="message in messages"
                     :message="message"
                     :chat-user="chatUser"
                     :user="user"
                     :class="{ 'ct-message--my': message.sender == user.id }"
            >
            </message>

        </div>

        <div class="ct-dialog__send-bar">
            <form id="ct-message__form" @click.prevent="saveMessage">
                <input type="hidden" name="sender" v-model="user">
                <input type="hidden" name="receiver" v-model="chatUser">
                <input autofocus autocomplete="off" class="ct-message__form__input" name="message" v-model="message"
                       placeholder="Write something..."/>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

</div>


<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="main.js"></script>
</body>
</html>
