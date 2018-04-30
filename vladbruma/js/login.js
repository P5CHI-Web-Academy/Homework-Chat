
(function($) {
    let toggle = $('.login-page__form-message a');

    toggle.click(function(e) {
        e.preventDefault();
        $('form').animate({height: 'toggle', opacity: 'toggle'}, 'slow');

        return false;
    });

    if (window.location.pathname === '/register') {
        toggle.first().trigger('click');
    }
})(jQuery);
