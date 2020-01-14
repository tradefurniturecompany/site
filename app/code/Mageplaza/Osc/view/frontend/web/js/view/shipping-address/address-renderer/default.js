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
    'Magento_Checkout/js/view/shipping-address/address-renderer/default',
    'Magento_Checkout/js/model/shipping-rate-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data'
], function (
    Component,
    shippingRateService,
    rateRegistry,
    quote,
    uiRegistry,
    selectBillingAddress,
    checkoutData
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Osc/container/address/shipping/address-renderer/default'
        },

        /** Set selected customer shipping address  */
        selectAddress: function () {
            if (!this.isSelected()) {
                this._super();

                if (quote.shippingAddress().getType == 'customer-address') {
                    rateRegistry.set(quote.shippingAddress().getKey(), null);
                } else {
                    rateRegistry.set(quote.shippingAddress().getCacheKey(), null);
                }
                var billingAddressComponent = uiRegistry.get("checkout.steps.shipping-step.billingAddress");
                if(billingAddressComponent.isAddressSameAsShipping()){
                    selectBillingAddress(quote.shippingAddress());
                    checkoutData.setSelectedBillingAddress(null);
                }

                shippingRateService.isAddressChange = true;
                shippingRateService.estimateShippingMethod();
            }
        }
    });
});
