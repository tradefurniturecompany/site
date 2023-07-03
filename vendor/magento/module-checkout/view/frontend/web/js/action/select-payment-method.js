/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    return function (paymentMethod) {
		// 2023-07-03 Dmitrii Fediuk https://www.upwork.com/fl/mage2pro
		// 1) A temporary quick and dirty workaround for the problem:
		// «Property "DisableTmpl" does not have accessor method "getDisableTmpl"
		// in class "Magento\Quote\Api\Data\PaymentInterface"»: https://github.com/tradefurniturecompany/site/issues/259
		// 2) https://github.com/PayboxByVerifone/Magento-2.3.x/issues/4#issuecomment-598288973
        /*if (paymentMethod) {
            paymentMethod.__disableTmpl = {
                title: true
            };
        }*/
        quote.paymentMethod(paymentMethod);
    };
});
