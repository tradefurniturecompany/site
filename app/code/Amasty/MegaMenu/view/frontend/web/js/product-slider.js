define([
    'jquery',
    'Amasty_Base/vendor/slick/slick.min'
], function ($) {
    $.widget('amasty.productSlider', {
        options: {},

        _create: function () {
            $(this.element).slick(this.options);

            $(this.element).parents('.ammenu-item.-main').on('mouseenter', function () {
                $(this.element).slick('setPosition');
            }.bind(this))
        }
    });

    return $.amasty.productSlider;
});
