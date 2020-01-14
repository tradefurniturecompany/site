define(['jquery'], function ($) {
    "use strict";
    return function change() {

        $("#checkout-loader").ready(function () {
            var existCondition = setInterval(function () {
                if ($('.payment-method').length) {
                    clearInterval(existCondition);
                    runMyFunction();
                }
            }, 200);

            function runMyFunction() {
                let deliveryWrapper = $('.table-checkout-shipping-method input:radio:checked');
                if (deliveryWrapper.length === 0) {
                    let finance = document.getElementById('financegateway');
                    if (finance) {
                        let parentFinance = finance.closest("div.payment-method");
                        parentFinance.style.display = 'none';
                    }
                    let payOnCollection = document.getElementById('cashondelivery1');
                    let parentPayOnCollection = payOnCollection.closest("div.payment-method");
                    parentPayOnCollection.style.display = 'none';
                }
            }
        });
    }
});