/**
 * Copyright © 2019 Imegamedia. All rights reserved.
*/
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'imegapayment',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/url'
    ],
    function (Component, imegapayment, quote, redirectOnSuccessAction, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Imega_FinanceGateway/payment/form'
            },

            getCode: function() {
                return 'financegateway';
            },

            getInstructions: function() {
                return window.checkoutConfig.payment.immFinanceGateway.instructions;
            },

            getTotal: function() {
              var total;
              var totalSegments = quote.getTotals()()['total_segments'];
              totalSegments.forEach(function(totalValues){
                if (totalValues.code == 'grand_total') {
                  total = totalValues.value;
                }
              });

              return total;
            },

            showFinanceOptions: function() {
              if(window.checkoutConfig.payment.immFinanceGateway.checkoutOnPayment){
                function onlyUnique(value, index, self) {
                  return self.indexOf(value) === index;
                }

                var filters = [];
                var financeFilter;
                window.checkoutConfig.quoteItemData.forEach(function(quoteItem){
                  filters.push(quoteItem.product.imegamedia_finance_filter)
                });
                var unique = filters.filter(onlyUnique);
                if(unique.length===1 && unique[0]){
                  financeFilter = unique[0];
                } else {
                  financeFilter = 'ALL';
                }

                imegapayment.init({
                  amount:  this.getTotal(),
                  priceElement: window.checkoutConfig.payment.immFinanceGateway.priceElement,
                  priceElementInner: window.checkoutConfig.payment.immFinanceGateway.priceElementInner,
                  apiKey: window.checkoutConfig.payment.immFinanceGateway.key,
                  financeFilter: financeFilter,
                  description: "",
                  insertion: window.checkoutConfig.payment.immFinanceGateway.insertion,
                  element: window.checkoutConfig.payment.immFinanceGateway.element
                });
              }
            },

            redirectAfterPlaceOrder: true,

            afterPlaceOrder: function() {
              if(window.checkoutConfig.payment.immFinanceGateway.checkoutOnPayment){
                var financeCode = window.checkoutConfig.payment.immFinanceGateway.financeCode;
                var deposit = window.checkoutConfig.payment.immFinanceGateway.deposit;
                redirectOnSuccessAction.redirectUrl = url.build('financegateway?finance_code='+financeCode+'&deposit='+deposit);
                this.redirectAfterPlaceOrder = true;
              }
            }

        });
    }
);
