/**
 * Copyright © 2019 Imegamedia. All rights reserved.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'financegateway',
                component: 'Imega_FinanceGateway/js/view/payment/method-renderer/financegateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
