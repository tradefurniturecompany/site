/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Mageplaza_Osc/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor',
    'uiRegistry',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Customer/js/customer-data'
], function (
    resourceUrlManager,
    quote,
    storage,
    shippingService,
    rateRegistry,
    errorProcessor,
    uiRegistry,
    paymentService,
    methodConverter,
    customerData
) {
    'use strict';

    return {
        /**
         * Get shipping rates for specified address.
         * @param {Object} address
         */
        getRates: function (address) {
            var cache, serviceUrl, payload;

            shippingService.isLoading(true);
            cache = rateRegistry.get(address.getCacheKey());
            serviceUrl = resourceUrlManager.getOscUrlForEstimationShippingMethodsForNewAddress(quote);
            var isAddressSameAsShipping = uiRegistry.get("checkout.steps.shipping-step.billingAddress").isAddressSameAsShipping();
            payload = JSON.stringify({
                    address: {
                        'street': address.street,
                        'city': address.city,
                        'region_id': address.regionId,
                        'region': address.region,
                        'country_id': address.countryId,
                        'postcode': address.postcode,
                        'email': address.email,
                        'customer_id': address.customerId,
                        'firstname': address.firstname,
                        'lastname': address.lastname,
                        'middlename': address.middlename,
                        'prefix': address.prefix,
                        'suffix': address.suffix,
                        'vat_id': address.vatId,
                        'company': address.company,
                        'telephone': address.telephone,
                        'fax': address.fax,
                        'custom_attributes': address.customAttributes,
                        'save_in_address_book': address.saveInAddressBook
                    },
                    isAddressSameAsShipping
                }
            );

            if (cache) {
                shippingService.setShippingRates(cache);
                shippingService.isLoading(false);
            } else {
                storage.post(
                    serviceUrl, payload, false
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
                });
            }
        }
    };
});
