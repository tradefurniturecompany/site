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
	'Magento_Checkout/js/view/payment/default',
	'mage/template',
	'mage/storage',
	'mage/url',
	'Magento_Checkout/js/model/url-builder',
	'Magento_Customer/js/model/customer',
	'Magento_Checkout/js/action/place-order',
	'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/error-processor',
	'Magento_Checkout/js/model/full-screen-loader',
	'Magento_Checkout/js/model/payment/additional-validators',
	'Magento_Checkout/js/model/payment/method-list',
	'Customweb_RealexCw/js/checkout',
	'Customweb_RealexCw/js/authorizationMethod',
	'Customweb_RealexCw/js/alias'
], function(
	$,
	Component,
	mageTemplate,
	storage,
	url,
	urlBuilder,
	customer,
	placeOrderAction,
	quote,
	errorProcessor,
	fullScreenLoader,
	additionalValidators,
	methodList,
	Form,
	AuthorizationMethod,
	Alias
) {
	'use strict';

	return Component.extend({
		defaults: {
			template: 'Customweb_RealexCw/payment/realexcw_directdebits',
			fieldErrorTemplate: '<div for="<%- id %>" generated="true" class="mage-error" id="<%- id %>-error"><%- message %></div>',
			authorizationUrl: url.build('realexcw/checkout/authorize/')
		},

		/**
		 * @override
		 */
		initialize: function() {
			this._super();

			Form.fieldErrorTmpl = mageTemplate(this.fieldErrorTemplate);

			this.authorizationMethod = AuthorizationMethod(this.getAuthorizationMethod(), this.getFormElementSelector(), this.authorizationUrl);

			this.alias = new Alias(this.getFormElementSelector(), this.item.method, $.proxy(this.onAliasUpdate, this));
			this.alias.attachListeners();

			this.preload();
			
			methodList.subscribe($.proxy(function(methods){
				if (methods) {
					this.getForm();
				}
			}, this));
			
			quote.paymentMethod.subscribe($.proxy(function(method){
				if (method && method.method == this.getCode()) {
					this.getForm();
				}
			}, this));

			return this;
		},

		/**
		 * @param object data
		 * @param object event
		 */
		cwPlaceOrder: function(data, event) {
			var self = this;

			if (event) {
				event.preventDefault();
			}

			Form.validate(this.item.method, function() {
				self.onValidateSuccess(data, event);
			}, function() {
				self.onValidateFailure();
			});
		},

		/**
		 * @param object data
		 * @param object event
		 * @return void
		 */
		onValidateSuccess: function(data, event) {
			this.placeOrder(data, event);
		},

		/**
		 * @return void
		 */
		onValidateFailure: function() {},

		/**
		 * @override
		 */
		redirectAfterPlaceOrder: false,

		/**
		 * @override
		 */
		afterPlaceOrder: function(orderId) {
			var self = this;
			fullScreenLoader.startLoader();
			this.authorizationMethod.authorize(orderId).fail(function(response) {
				fullScreenLoader.stopLoader();
				errorProcessor.process(response, self.messageContainer);
			});
		},
		
		/**
		 * @override
		 */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            if (this.validate() && additionalValidators.validate()) {
                this.isPlaceOrderActionAllowed(false);

                var placeOrder;
                if (this.getPlaceOrderDeferredObject) {
                		placeOrder = this.getPlaceOrderDeferredObject();
            		} else {
                		placeOrder = $.when(placeOrderAction(this.getData(), this.redirectAfterPlaceOrder, this.messageContainer));
            		}
                placeOrder
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                        function (orderId) {
                            self.afterPlaceOrder(orderId);
                        }
                    );

                return true;
            }

            return false;
        },

		/**
		 * @override
		 */
		getData: function() {
			var parent = this._super(),
				additionalData = {};
			$.each(Form.getValues($(this.getFormElementSelector()), false), function(key, value) {
				additionalData['form[' + key + ']'] = value;
			});
			if (this.alias.getValue()) {
				additionalData['alias'] = this.alias.getValue();
			}
			return $.extend(true, parent, {
				'additional_data': additionalData
			});
		},

		/**
		 * @param object response
		 * @return void
		 */
		onAliasUpdate: function(response) {
			$(this.getFormElementSelector()).html(this.updateForm(response.html));
		},

		/**
		 * @param string formContent
		 * @return string
		 */
		updateForm: function(formContent) {
			var $form = $('<div>').append(formContent);

			this.alias.updateForm($form);

			if (this.authorizationMethod.formDataProtected()) {
				Form.removeFieldNames($form);
			}

			return $form.html();
		},

		/**
		 * Preload the payment method after a failure and show the error message.
		 * 
		 * @return void
		 */
		preload: function() {
			if (this.getFailureMessage()) {
				this.selectPaymentMethod();
				errorProcessor.process({
					status: 500,
					responseText: JSON.stringify({
						message: this.getFailureMessage()
					})
				}, this.messageContainer);
			}
		},

		/**
		 * Retrieve true if the method image should be displayed.
		 * 
		 * @return boolean
		 */
		isShowImage: function() {
			return window.checkoutConfig.payment.show_image[this.item.method];
		},

		/**
		 * Retrieve the image file url.
		 * 
		 * @return string
		 */
		getImageUrl: function() {
			return window.checkoutConfig.payment.image_url[this.item.method];
		},

		/**
		 * Retrieve the description text.
		 * 
		 * @return string
		 */
		getDescription: function() {
			return window.checkoutConfig.payment.description[this.item.method];
		},

		/**
		 * Retrieve the payment form.
		 * 
		 * @return string
		 */
		getForm: function() {
			var self = this;
			
			var serviceUrl;
			if (!customer.isLoggedIn()) {
				serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/realexcw/checkout/payment-form/:paymentMethod', {
					cartId: quote.getQuoteId(),
					paymentMethod: this.item.method
				});
			} else {
				serviceUrl = urlBuilder.createUrl('/carts/mine/realexcw/checkout/payment-form/:paymentMethod', {
					paymentMethod: this.item.method
				});
			}
			storage.get(
				serviceUrl, false
			).done(function(response){
				self.onFieldUpdate(response);
			});
		},

		/**
		 * @param object response
		 * @return void
		 */
		onFieldUpdate: function(response) {
			$(this.getFormElementSelector()).html(this.updateForm(response.html));
		},

		/**
		 * Retrieve the failure message.
		 * 
		 * @return string
		 */
		getFailureMessage: function() {
			return window.checkoutConfig.payment.failureMessage[this.item.method];
		},

		/**
		 * Retrieve the authorization method.
		 * 
		 * @return string
		 */
		getAuthorizationMethod: function() {
			return window.checkoutConfig.payment.authorizationMethod[this.item.method];
		},

		/**
		 * Retrieve the form element's selector.
		 * 
		 * @return string
		 */
		getFormElementSelector: function() {
			return '#payment_form_' + this.item.method;
		}
	});
});