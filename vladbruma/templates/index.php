<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
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

<div class="ct-container">
    <div class="ct-contacts">
        <div id="ct-me" class="ct-me" data-id="{{ user['id'] }}">
            <div class="ct-avatar ct-avatar--large">
                <img src="img/avatar_circle_blue_512dp.png" alt="" class="ct-avatar__image">
            </div>
            <div class="ct-me__username" data-name="{{ user['name'] }}">
                {{ user['name'] }}
                <a href="/logout">
                    <img src="img/logout.png" class="ct-avatar__logout-image" alt="Logout" title="Logout">
                </a>
            </div>
            <div class="ct-me__email">
                {{ user['email'] }}
            </div>
        </div>
        <div class="ct-contact-container">
            {% for contact in allUsers %}
                <div id="ct-contact-{{ contact['id'] }}" class="ct-contact" data-id="{{ contact['id'] }}">
                    <div class="ct-contact__avatar-container">
                        <div class="ct-avatar ct-avatar--large">
                            <div class="ct-avatar__user-status"></div>
                            <img src="img/avatar_circle_blue_512dp.png" alt="" class="ct-avatar__image">
                        </div>
                    </div>
                    <div class="ct-contact__user-info-container">
                        <div class="ct-contact__user-name">{{ contact['name'] }}</div>
                        <div class="ct-contact__user-last-message">Idle</div>
                        <div class="ct-contact__user-unread-message-number"
                             {% if contact['unseen'] == 0 %}style="display: none;"{% endif %}>
                            <span>{{ contact['unseen'] }}</span>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    <div class="ct-dialog">
        <div class="ct-dialog__top-bar">
            <div class="ct-dialog__interlocutor" style="display:none;">
                <div class="ct-avatar ct-avatar--medium">
                    <img src="" alt="" class="ct-avatar__image">
                </div>
                <div class="ct-dialog__interlocutor-name">
                    Select contact
                </div>
            </div>
            <div class="ct-dialog__actions">
                <div class="ct-dialog__action">
                    <div class="ct-icon ct-icon--large ct-icon--grey ct-icon--hover-violet">
                        <img src="img/phone.png" alt="phone" class="ct-icon__svg">
                    </div>
                </div>
                <div class="ct-dialog__action">
                    <div class="ct-icon ct-icon--large ct-icon--grey ct-icon--hover-violet">
                        <img src="img/camera.png" alt="camera" class="ct-divider-vertical__icon">
                    </div>
                </div>
                <div class="ct-dialog__action">
                    <div class="ct-icon ct-icon--large ct-icon--grey ct-icon--hover-violet">
                        <img src="img/menu.png" alt="menu" class="ct-icon__svg">
                    </div>
                </div>
            </div>
        </div>

        <div id="ct-dialog__messages-templates" style="display: none;">
            <div id="ct-message-template-my" class="ct-message ct-message--my" >
                <div class="ct-message__user">
                    <div class="ct-avatar ct-avatar--small">
                        <img src="" alt="" class="ct-avatar__image">
                    </div>
                    <div class="ct-message__time">00:00</div>
                </div>
                <div class="ct-message__container">
                    <div class="ct-message__text">
                        Lorem ipsum dolor. <span class="ct-icon ct-icon--emoticon"><img src="" alt="svg"></span>
                    </div>
                </div>
            </div>
            <div id="ct-message-template" class="ct-message" >
                <div class="ct-message__container">
                    <div class="ct-message__text">
                        Lorem ipsum dolor.  Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor.
                        Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor.
                        Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor.
                        Lorem ipsum dolor. Lorem ipsum dolor. Lorem ipsum dolor.
                    </div>
                </div>
                <div class="ct-message__user">
                    <div class="ct-avatar ct-avatar--small">
                        <img src="" alt="" class="ct-avatar__image">
                    </div>
                    <div class="ct-message__time">00:00</div>
                </div>
            </div>
        </div>

        <div id="ct-dialog__messages-container" class="ct-dialog__messages-container">
            <div class="ct-dialog__start-text">
                Select contact from the list to begin
            </div>
        </div>

        <div class="ct-dialog__send-bar">
            <form id="ct-message__form" method="POST" class="ct-dialog__message-form" action="/post">
                <input id="ct-dialog__input" type="text" name="message" class="ct-dialog__send-bar-input" placeholder="Write something..." disabled required/>

                <button type="submit" class="ct-dialog__send-bar-send"><span>Send</span></button>
            </form>
        </div>
    </div>
</div>

<script src="vendor.js"></script>
<script src="scripts.js"></script>
</body>
</html>
