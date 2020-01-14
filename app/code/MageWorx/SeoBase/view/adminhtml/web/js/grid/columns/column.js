/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({

        /**
         * Returns field action handler if it was specified.
         *
         * @param {Object} record - Record object with which action is associated.
         * @returns {Function|Undefined}
         */
        getFieldHandler: function (record) {

            this.fieldAction.productId   = record.entity_id;
            this.fieldAction.productName = record.name;

            return this._super();
        }
    });
});