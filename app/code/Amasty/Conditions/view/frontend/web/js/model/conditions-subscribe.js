define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Amasty_Conditions/js/action/recollect-totals',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'underscore',
    'Magento_SalesRule/js/view/payment/discount'
], function (ko, $, Component, quote, recollect, shippingService, shippingProcessor, _, discount) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._insertPolyfills();
            this._super();
            var billingAddressCountry,
                city,
                street;

            discount().isApplied.subscribe(function () {
                recollect(true);
            });

            quote.shippingAddress.subscribe(function (newShippingAddress) {
                if (this._isNeededRecollectShipping(newShippingAddress, city, street)) {
                    city = newShippingAddress.city;
                    street = newShippingAddress.street;
                    if (newShippingAddress) {
                        recollect();
                    }
                }
            }.bind(this));

            quote.billingAddress.subscribe(function (newBillAddress) {
                if (this._isNeededRecollectBilling(newBillAddress, billingAddressCountry)) {
                    billingAddressCountry = newBillAddress.countryId;
                    shippingProcessor.getRates(quote.shippingAddress());
                    recollect();
                }
            }.bind(this));

            shippingService.isLoading.subscribe(function (isLoading) {
                if (!isLoading && !this._isVirtualQuote()) {
                    recollect();
                }
            }.bind(this));

            quote.paymentMethod.subscribe(recollect);
            quote.shippingMethod.subscribe(recollect);

            return this;
        },

        _isVirtualQuote: function () {
            return quote.isVirtual()
                || window.checkoutConfig.activeCarriers && window.checkoutConfig.activeCarriers.length === 0;
        },

        _isNeededRecollectShipping: function (newShippingAddress, city, street) {
            return !this._isVirtualQuote()
                && (
                    newShippingAddress
                    && (newShippingAddress.city || newShippingAddress.street)
                    && (newShippingAddress.city != city || !_.isEqual(newShippingAddress.street, street)));
        },

        _isNeededRecollectBilling: function (newBillAddress, billingAddressCountry) {
            return newBillAddress && newBillAddress.countryId && newBillAddress.countryId != billingAddressCountry
        },

        _insertPolyfills: function () {
            if (typeof Object.assign != 'function') {
                // Must be writable: true, enumerable: false, configurable: true
                Object.defineProperty(Object, "assign", {
                    value: function assign(target, varArgs) { // .length of function is 2
                        'use strict';
                        if (target == null) { // TypeError if undefined or null
                            throw new TypeError('Cannot convert undefined or null to object');
                        }

                        var to = Object(target);

                        for (var index = 1; index < arguments.length; index++) {
                            var nextSource = arguments[index];

                            if (nextSource != null) { // Skip over if undefined or null
                                for (var nextKey in nextSource) {
                                    // Avoid bugs when hasOwnProperty is shadowed
                                    if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                        to[nextKey] = nextSource[nextKey];
                                    }
                                }
                            }
                        }
                        return to;
                    },
                    writable: true,
                    configurable: true
                });
            }
        }
    });
});
