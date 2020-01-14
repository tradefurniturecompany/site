/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/editing/editor'
], function (_, Editor) {
    'use strict';

    return Editor.extend({
        defaults: {
            storeId: 0
        },

        /**
         * Validates and saves data of active records.
         *
         * @returns {Editor} Chainable.
         */
        save: function () {
            var data;
            var storeId;

            if (!this.isValid()) {
                return this;
            }

            storeId = this.storeId ? this.storeId : 0;

            data = {
                items: this.getData(),
                store_id: storeId
            };

            this.clearMessages()
                .columns('showLoader');

            this.client()
                .save(data)
                .done(this.onDataSaved)
                .fail(this.onSaveError);

            return this;
        }
    });
});
