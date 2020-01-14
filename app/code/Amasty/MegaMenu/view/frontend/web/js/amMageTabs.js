define([
    'jquery'
], function ($) {
    'use strict';

    return function (amMageTabs) {
        //overwritting magento _closeOthers method for animate collapsing inactive accordion tabs
        $.widget('mage.tabs', amMageTabs, {
            _closeOthers: function () {
                var self = this;

                $.each(this.collapsibles, function () {
                    $(this).on('beforeOpen', function () {
                        self.collapsibles.not(this).collapsible('deactivate');
                    });
                });
            },
        });

        return $.mage.tabs;
    }
});