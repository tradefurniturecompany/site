/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (uiRegistry, select) {
    'use strict';

    return select.extend({
        defaults: {
            dependentFields: {}
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            this.dependentFields.targetUrlField      = uiRegistry.get('index = ' + this.indexies.target_url);
            this.dependentFields.targetProductField  = uiRegistry.get('index = ' + this.indexies.target_product);
            this.dependentFields.targetCategoryField = uiRegistry.get('index = ' + this.indexies.target_category);
            this.dependentFields.targetCmsPageField  = uiRegistry.get('index = ' + this.indexies.target_cms_page);

            for (var field in this.dependentFields) {

                if (this.dependentFields[field].visibleValue === value) {
                    this.dependentFields[field].show();
                } else {
                    this.dependentFields[field].hide();
                }
            }

            return this._super();
        }
    });
});