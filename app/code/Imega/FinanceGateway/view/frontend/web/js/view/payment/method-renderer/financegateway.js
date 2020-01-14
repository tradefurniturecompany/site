/**
 * Copyright © 2019 Imegamedia. All rights reserved.
*/
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Imega_FinanceGateway/payment/form'
            },

            getCode: function() {
                return 'financegateway';
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },

            redirectAfterPlaceOrder: true

        });
    }
);
