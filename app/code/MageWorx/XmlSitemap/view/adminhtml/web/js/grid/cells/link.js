/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
    ],
    function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'MageWorx_XmlSitemap/grid/cells/link',

        },

        getFieldHandler: function (record) {
            return false;
        },

    });
});
