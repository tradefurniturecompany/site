/*
 * Copyright (c) 2017 www.tigren.com
 */

define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Tigren_MultiCOD/payment/cashondelivery1'
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
);
