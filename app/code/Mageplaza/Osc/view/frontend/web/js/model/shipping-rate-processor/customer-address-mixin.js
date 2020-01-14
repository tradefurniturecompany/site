/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'Mageplaza_Osc/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Customer/js/customer-data',
    'uiRegistry'
], function (
    resourceUrlManager,
    quote,
    storage,
    shippingService,
    rateRegistry,
    errorProcessor,
    paymentService,
    methodConverter,
    customerData,
    uiRegistry) {
    'use strict';

    return {
        /**
         * @param {Object} address
         */
        getRates: function (address) {
            var cache;

            shippingService.isLoading(true);
            cache = rateRegistry.get(address.getKey());

            if (cache) {
                shippingService.setShippingRates(cache);
                shippingService.isLoading(false);
            } else {
                var isAddressSameAsShipping = uiRegistry.get("checkout.steps.shipping-step.billingAddress").isAddressSameAsShipping();
                storage.post(
                    resourceUrlManager.getOscUrlForEstimationShippingMethodsByAddressId(),
                    JSON.stringify({
                        addressId: address.customerAddressId,
                        isAddressSameAsShipping
                    }),
                    false
                ).done(function (response) {
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                        return;
                    }
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    if (response.shipping_methods && !quote.isVirtual()) {
                        rateRegistry.set(address.getKey(), response.shipping_methods);
                        shippingService.setShippingRates(response.shipping_methods);
                    }
                    customerData.reload(['cart'], true);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                        shippingService.isLoading(false);
                    }
                );
            }
        }
    };
});