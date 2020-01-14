/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/components/group'
], function (uiRegistry, Group) {
    'use strict';

    return Group.extend({

        /**
         * Set Product Id and Product Name
         *
         * @param {String} productId
         * @param {String} productName
         */
        selectProduct: function (productId, productName) {

            var productIdField     = uiRegistry.get('index = ' + this.indexies.product_id);
            var productLabelField  = uiRegistry.get('index = ' + this.indexies.product_label);
            var productGridModal   = uiRegistry.get('index = ' + this.indexies.product_grid_modal);

            productIdField.value(productId);
            productLabelField.value(productName);
            productGridModal.toggleModal();
        },

        /**
         * Show element.
         *
         * @returns {Group} Chainable.
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element.
         *
         * @returns {Group} Chainable.
         */
        hide: function () {
            this.visible(false);

            return this;
        }
    });
});