/**
 * Copy to extension because it is missing in magento before 2.3.0
 */

/**
 * @api
 */
define([
    'mage/translate',
    'Magento_Ui/js/form/element/abstract',
    'Amasty_MegaMenu/js/form/element/color-picker-palette'
], function ($t, Abstract, palette) {
    'use strict';

    return Abstract.extend({

        defaults: {
            colorPickerConfig: {
                chooseText: $t('Apply'),
                cancelText: $t('Cancel'),
                maxSelectionSize: 8,
                clickoutFiresChange: true,
                allowEmpty: true,
                localStorageKey: 'magento.spectrum',
                palette: palette
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();

            this.colorPickerConfig.value = this.value;

            return this;
        }
    });
});
