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

define(
    [
        'underscore',
        'jquery',
        'Magento_Checkout/js/view/summary/item/details',
        'Magento_Checkout/js/model/quote',
        'Mageplaza_Osc/js/action/update-item',
        'Mageplaza_Osc/js/action/gift-message-item',
        'mage/url',
        'mage/translate',
        'Magento_Ui/js/modal/modal'
    ],
    function (_, $, Component, quote, updateItemAction, giftMessageItem, url, $t, modal) {
        "use strict";

        var products = window.checkoutConfig.quoteItemData,
            giftMessageOptions = window.checkoutConfig.oscConfig.giftMessageOptions,
            qtyIncrements = window.checkoutConfig.oscConfig.qtyIncrements;


        return Component.extend({
            defaults: {
                template: 'Mageplaza_Osc/container/summary/item/details'
            },
            giftMessageItemsTitleHover: $t('Gift message item'),
            updateQtyDelay: 500,
            updateQtyTimeout: 0,

            /**
             * Get product url
             * @param parent
             * @returns {*}
             */
            getProductUrl: function (parent) {
                var item = _.find(products, function (product) {
                    return product.item_id == parent.item_id;
                });

                if (item && item.hasOwnProperty('product') &&
                    item.product.hasOwnProperty('request_path') && item.product.request_path) {
                    return url.build(item.product.request_path);
                }

                return false;
            },

            /**
             * Init popup gift message item window
             * @param element
             */
            setModalElement: function (element, item_id) {
                var self = this;
                this.modalWindow = element;
                var options = {
                    'type': 'popup',
                    'title': $t('Gift Message Item &#40' + element.title + '&#41'),
                    'modalClass': 'popup-gift-message-item',
                    'responsive': true,
                    'innerScroll': true,
                    'trigger': '#' + element.id,
                    'buttons': [],
                    'opened': function () {
                        self.loadGiftMessageItem(item_id);
                    }
                };
                modal(options, $(this.modalWindow));
            },

            /**
             * Load exist gift message item from
             * @param itemId
             */
            loadGiftMessageItem: function (itemId) {
                $('.popup-gift-message-item._show #item' + itemId).find('input:text,textarea').val('');
                if (giftMessageOptions.giftMessage.itemLevel[itemId].hasOwnProperty('message')
                    && typeof giftMessageOptions.giftMessage.itemLevel[itemId]['message'] == 'object') {
                    var giftMessageItem = giftMessageOptions.giftMessage.itemLevel[itemId]['message'];
                    $(this.createSelectorElement(itemId + ' #gift-message-whole-from')).val(giftMessageItem.sender);
                    $(this.createSelectorElement(itemId + ' #gift-message-whole-to')).val(giftMessageItem.recipient);
                    $(this.createSelectorElement(itemId + ' #gift-message-whole-message')).val(giftMessageItem.message);
                    $(this.createSelectorElement(itemId + ' .action.delete')).show();
                    return this;
                }

                $(this.createSelectorElement(itemId + ' .action.delete')).hide();
            },

            /**
             * create selector element
             * @param selector
             * @returns {string}
             */
            createSelectorElement: function (selector) {
                return '.popup-gift-message-item._show #item' + selector;
            },

            /**
             * Update gift message item
             * @param itemId
             */
            updateGiftMessageItem: function (itemId) {
                var data = {
                    gift_message: {
                        sender: $(this.createSelectorElement(itemId + ' #gift-message-whole-from')).val(),
                        recipient: $(this.createSelectorElement(itemId + ' #gift-message-whole-to')).val(),
                        message: $(this.createSelectorElement(itemId + ' #gift-message-whole-message')).val()
                    }
                };
                giftMessageItem(data, itemId, false);
                this.closePopup();
            },
            /**
             * Delete gift message item
             * @param itemId
             */
            deleteGiftMessageItem: function (itemId) {
                giftMessageItem({
                    gift_message: {
                        sender: '',
                        recipient: '',
                        message: ''
                    }
                }, itemId, true);
                this.closePopup();
            },

            /**
             * Close popup gift message item
             */
            closePopup: function () {
                $('.action-close').trigger('click');
            },

            /**
             * Check item is available
             * @param itemId
             * @returns {boolean}
             */
            isItemAvailable: function (itemId) {
                var isGloballyAvailable,
                    itemConfig;
                var item = _.find(products, function (product) {
                    return product.item_id == itemId;
                });
                if (item.is_virtual == true || !giftMessageOptions.isEnableOscGiftMessageItems) return false;

                // gift message product configuration must override system configuration
                isGloballyAvailable = this.getConfigValue('isItemLevelGiftOptionsEnabled');
                itemConfig = giftMessageOptions.giftMessage.hasOwnProperty('itemLevel')
                && giftMessageOptions.giftMessage.itemLevel.hasOwnProperty(itemId) ?
                    giftMessageOptions.giftMessage.itemLevel[itemId] : {};

                return itemConfig.hasOwnProperty('is_available') ? itemConfig['is_available'] : isGloballyAvailable;
            },
            getConfigValue: function (key) {
                return giftMessageOptions.hasOwnProperty(key) ?
                    giftMessageOptions[key]
                    : false;
            },

            /**
             * Plus item qty
             *
             * @param item
             * @param event
             */
            plusQty: function (item, event) {
                var self = this;

                clearTimeout(this.updateQtyTimeout);

                var target = $(event.target).prev().children(".item_qty"),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId)) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.floor(qty / qtyDelta) + 1) * qtyDelta;
                } else {
                    qty += 1;
                }

                target.val(qty);

                this.updateQtyTimeout = setTimeout(function () {
                    self.updateItem(itemId, qty, target)
                }, this.updateQtyDelay);
            },

            /**
             * Minus item qty
             *
             * @param item
             * @param event
             */
            minusQty: function (item, event) {
                var self = this;

                clearTimeout(this.updateQtyTimeout);

                var target = $(event.target).next().children(".item_qty"),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId)) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.ceil(qty / qtyDelta) - 1) * qtyDelta;
                } else {
                    qty -= 1;
                }

                target.val(qty);

                this.updateQtyTimeout = setTimeout(function () {
                    self.updateItem(itemId, qty, target)
                }, this.updateQtyDelay);
            },

            /**
             * Change item qty in input box
             *
             * @param item
             * @param event
             */
            changeQty: function (item, event) {
                var target = $(event.target),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId) && (qty % qtyIncrements[itemId])) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.ceil(qty / qtyDelta) - 1) * qtyDelta;
                }

                this.updateItem(itemId, qty, target);
            },

            /**
             * Remove item by id
             *
             * @param itemId
             */
            removeItem: function (itemId) {
                this.updateItem(itemId);
            },

            /**
             * Send request update item
             *
             * @param itemId
             * @param itemQty
             * @param target
             * @returns {*}
             */
            updateItem: function (itemId, itemQty, target) {
                var self = this,
                    payload = {
                        item_id: itemId
                    };

                if (typeof itemQty !== 'undefined') {
                    payload['item_qty'] = itemQty;
                }

                updateItemAction(payload).fail(function (response) {
                    target.val(self.getProductQty(itemId));
                });

                return this;
            },

            /**
             * Get product quantity
             * @param itemId
             * @returns {*}
             */
            getProductQty: function (itemId) {
                var item = _.find(quote.totals().items, function (product) {
                    return product.item_id == itemId;
                });

                if (item && item.hasOwnProperty('qty')) {
                    return item.qty;
                }

                return 0;
            }
        });
    }
);
