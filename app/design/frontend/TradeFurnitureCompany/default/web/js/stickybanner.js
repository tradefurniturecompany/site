define([
    'jquery'
], function ($) {

    $.widget('interactivated.stickyBanner', {
        options: {
            stickyStatus: 0
        },

        _create: function () {
            var isStickyBanner = this.options.stickyStatus;

            if (isStickyBanner) {
                var banner = $('[data-stickybanner-js="sticky-banner"]'),
                    bannerOffsetTop = banner.offset().top;

                $(window).on('scroll', function () {
                    banner.toggleClass('sticky', window.pageYOffset >= bannerOffsetTop);
                });
            }

        },

    });

    return $.interactivated.stickyBanner;
});
