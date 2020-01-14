/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

define([
	'jquery',
	'mage/storage',
	'Magento_Checkout/js/model/url-builder',
	'Magento_Customer/js/model/customer',
	'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/full-screen-loader'
], function(
	$,
	storage,
	urlBuilder,
	customer,
	quote,
	fullScreenLoader
) {
	'use strict';

	/**
	 * Alias Class
	 * 
	 * @param string formElement
	 * @param object updateData
	 * @param function updateCallback
	 * @param string updateUrl
	 */
	var Alias = function(formElement, paymentMethod, updateCallback) {
		/**
		 * @return string
		 */
		this.getValue = function() {
			var aliasElement = $(formElement).find('[data-field-alias="select"]'),
				aliasCreateElement = $(formElement).find('[data-field-alias="create"]'),
				alias = aliasElement.length ? aliasElement.val() : null,
				aliasCreate = aliasCreateElement.length ? aliasCreateElement.prop('checked') : false;
			if (alias != null && alias != '') {
				return alias;
			} else if (aliasCreate) {
				return 'new';
			} else {
				return null;
			}
		}

		/**
		 * @param object $form
		 * @return void
		 */
		this.updateForm = function($form) {
			$form.find('*[name="alias[create]"]').attr('name', '').attr('data-field-alias', 'create');
			$form.find('*[name="alias[select]"]').attr('name', '').attr('data-field-alias', 'select');
		}

		/**
		 * @return void
		 */
		this.attachListeners = function() {
			var self = this;

			$(document).on('change', formElement + ' [data-field-alias="select"]', function() {
				var serviceUrl;
				if (!customer.isLoggedIn()) {
					serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/realexcw/checkout/payment-form/:paymentMethod', {
						cartId: quote.getQuoteId(),
						paymentMethod: paymentMethod
					}) + '?alias=' + self.getValue() || 0;
				} else {
					serviceUrl = urlBuilder.createUrl('/carts/mine/realexcw/checkout/payment-form/:paymentMethod', {
						paymentMethod: paymentMethod
					}) + '?alias=' + self.getValue() || 0;
				}
				fullScreenLoader.startLoader();
				storage.get(
					serviceUrl, false
				).done(updateCallback).always(function(){
					fullScreenLoader.stopLoader();
				});
			});
		}
	}

	return Alias;
});