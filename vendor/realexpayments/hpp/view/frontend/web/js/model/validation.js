define(
    [
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/quote',
		'jquery'
    ],
    function ($t, messageList, quote, $) {
        'use strict';
        var errorMessages = {
            invalidShippingAddress:  $t('Please check the Shipping address. '),
            invalidBillingAddress:  $t('Please check the Billing address. '),
            invalidName:  $t('Invalid First name or Last name. The selected payment method only allows letters, numbers, spaces or punctuation, and no more than 31 characters in the 2 fields.'),
            invalidStreet:  $t('Invalid Street address. The selected payment method only allows letters, numbers, spaces or punctuation, and no more than 40 characters per line.'),
            invalidCity:  $t('Invalid City. The selected payment method only allows letters, numbers, spaces or punctuation, and no more than 40 characters.'),
            invalidZipCode:  $t('Invalid Zip/Postal Code. The selected payment method only allows letters, numbers, spaces or punctuation, and no more than 16 characters.'),
            invalidPhone:  $t('Invalid Telephone. The selected payment method only allows numbers, spaces or punctuation (+, |), and no more than 19 characters.')
        };

        return {
            validate: function () {
                var paymentMethod = quote.paymentMethod().method;

                if (paymentMethod != 'realexpayments_hpp') {
                    return true;
                }

                var shippingAddress = quote.shippingAddress(),
                    billingAddress = quote.billingAddress();

                /**
                 * Customer edits his address, and forgets to save it
                 * Magento will throw an error
                 */
                if (!billingAddress) {
                    return true;
                }

                /**
                 * Normal flow
                 */
                if (shippingAddress && shippingAddress.firstname && shippingAddress.lastname) {
					// 2022-11-25 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					// 1) [Global Payments / Realex] The «Place order» button is not working sometimes:
					// «Cannot read properties of undefined (reading 'forEach') at Object.isValidStreet»:
					// https://github.com/tradefurniturecompany/site/issues/239
					// 2) https://caniuse.com/?search=Logical%20nullish
					// 3) https://stackoverflow.com/a/55685094
					// 4) https://stackoverflow.com/a/62824667
					const street = [$("input[name='street[0]']").val(), $("input[name='street[1]']").val()].filter(i => i);
					shippingAddress.street ||= street;
					billingAddress.street ||= street;
                    return (this.isValidAddress(shippingAddress, errorMessages.invalidShippingAddress) &&
                        this.isValidAddress(billingAddress, errorMessages.invalidBillingAddress));
                }

                /**
                 * Virtual product in cart and guest user (no shipping address provided by Magento)
                 */
                return this.isValidAddress(billingAddress, errorMessages.invalidBillingAddress);
            },

            /**
             * Validate address
             *
             * @param address
             * @param {String} errorMessagePrefix
             * @returns {Boolean}
             */
            isValidAddress: function (address, errorMessagePrefix) {
                if (!this.isValidName(address.firstname, address.lastname)) {
                    this.showError(errorMessagePrefix + errorMessages.invalidName);
                    return false;
                }
                if (!this.isValidStreet(address.street)) {
                    this.showError(errorMessagePrefix + errorMessages.invalidStreet);
                    return false;
                }
                if (!this.isValidCity(address.city)) {
                    this.showError(errorMessagePrefix + errorMessages.invalidCity);
                    return false;
                }
                if (!this.isValidZipCode(address.postcode)) {
                    this.showError(errorMessagePrefix + errorMessages.invalidZipCode);
                    return false;
                }
                if (!this.isValidPhone(address.telephone)) {
                    this.showError(errorMessagePrefix + errorMessages.invalidPhone);
                    return false;
                }

                return true;
            },

            /**
             * Validate name
             *
             * @param {String} firstname
             * @param {String} lastname
             * @returns {Boolean}
             */
            isValidName: function (firstname, lastname) {
                let pattern = /^[ÀÁÂÃÂÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷ø¤ùúûüýþÿ~L~N~Z~\~^~_¥a-zA-Z0-9.'";\s,\+\-£\/@!\?%\(\)\*:$#\[\]|=\\&amp;\u0152\u0153\u017D\u0161\u017E\u0178\u20AC]*$/;

                return (pattern.test(firstname) && pattern.test(lastname) && (firstname.length + lastname.length)<=31);
            },

            /**
             * Validate address
             *
             * @param {Array} street
             * @return {Boolean}
             */
            isValidStreet: function (street) {
				// 2022-11-25 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				// 1) [Global Payments / Realex] The «Place order» button is not working sometimes:
				// «Cannot read properties of undefined (reading 'forEach') at Object.isValidStreet»:
				// https://github.com/tradefurniturecompany/site/issues/239
				var isValid = !!street;
				if (isValid) {
					let pattern = /^[ÀÁÂÃÂÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ_a-zA-Z0-9.'\s,\-\/\u0152\u0153\u017D\u0161\u017E\u0178]*$/;
					street.forEach(function (item) {
						if (!pattern.test(item) || item.length>40) {
							isValid = false;
						}
					});
				}
                return isValid;
            },

            /**
             * Validate city
             *
             * @param {String} city
             * @return {Boolean}
             */
            isValidCity: function (city) {
                let pattern = /^[ÀÁÂÃÂÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ_a-zA-Z0-9.'\s,\-\/\u0152\u0153\u017D\u0161\u017E\u0178]*$/;

                return pattern.test(city) && city.length<=40;
            },

            /**
             * Validate zipcode
             *
             * @param {String} zipcode
             * @return {Boolean}
             */
            isValidZipCode: function (zipcode) {
                let pattern = /^[a-zA-Z0-9-\s]{1,16}$/;

                return pattern.test(zipcode);
            },

            /**
             * Validate phone
             *
             * @param {String} phone
             * @return {Boolean}
             */
            isValidPhone: function (phone) {
                let pattern = /^([0-9 +]){1,3}(\|){0,1}([0-9 +]){1,15}$/;

                return pattern.test(phone);
            },

            /**
             * Show error message
             *
             * @param {String} errorMessage
             */
            showError: function (errorMessage) {
                messageList.addErrorMessage({
                    message: errorMessage
                });
            },
        }
    }
);