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
	'jquery'
], function(
	$
) {
	'use strict';

	/**
	 * Form Class
	 * 
	 * @param string url
	 * @param object fields
	 */
	var Form = function(url, fields) {
		var renderDataAsHiddenFields = function(data) {
			var output = '';
			$.each(data, function(key, value) {
				if ($.isArray(value)) {
					for (var i = 0; i < value.length; i++) {
						output += renderHiddenField(key + '[]', value[i]);
					}
				} else {
					output += renderHiddenField(key, value);
				}
			});
			return output;
		}

		var renderHiddenField = function(key, value) {
			if (typeof value == 'string') {
				value = value.replace(/"/g, "&quot;");
			}
			return '<input type="hidden" name="' + key + '" value="' + value + '" />';
		}

		var createElement = function() {
			var formElement = '<form action="' + url + '" method="POST">';
			formElement += renderDataAsHiddenFields(fields);
			formElement += '</form>';
			return $(formElement);
		}

		/**
		 * Submit the data via POST to the url.
		 * 
		 * @return void
		 */
		this.submit = function() {
			var formElement = createElement();
			$('body').append(formElement);
			formElement.submit();
		}
	}

	/**
	 * Form validation registry
	 */
	Form.Validation = new(function() {
		var validators = [];

		/**
		 * Register a js function postfix.
		 * 
		 * @param string group
		 * @param function js postfix
		 */
		this.register = function(group, postfix) {
			validators[group] = postfix;
		},

		/**
		 * Run the registered validators in the given group.
		 * 
		 * @param group
		 * @param function callback
		 */
		this.validate = function(group, successCallback, failureCallback) {
			var formId = $('form[name="' + group + '"]').attr('id');

			var postfix = validators[group];
			if (typeof postfix === 'undefined') {
				successCallback(new Array());
				return;
			}

			var validateFunctionName = 'cwValidateFields' + postfix;
			var validateFunction = window[validateFunctionName];

			if (typeof validateFunction != 'undefined') {
				validateFunction(successCallback, failureCallback);
				return;
			}
			successCallback(new Array());
		}
	})();

	/**
	 * Remove the form field name attributes of an entire form to prevent the sending them to the server.
	 * 
	 * @return void
	 */
	Form.removeFieldNames = function(formElement) {
		var submittableTypes = ['select', 'input', 'button', 'textarea'];
		for(var i = 0; i < submittableTypes.length; i++) {
			formElement.find(submittableTypes[i] + '[name]').each(function(key, element) {
				Form.removeFieldName(element);
			});
		}
	}

	/**
	 * Remove the form field name attribute of a single element to prevent the sending them to the server.
	 * 
	 * @return void
	 */
	Form.removeFieldName = function(element) {
		$(element).attr('data-field-name', $(element).attr('name'));
		$(element).removeAttr('name');
	}

	/**
	 * Get the the values of a form.
	 * 
	 * @param object element
	 * @param boolean dataProtected
	 * @return object
	 */
	Form.getValues = function(formElement, dataProtected) {
		var output = {};
		var nameAttribute = dataProtected ? 'data-field-name' : 'name';
		formElement.find('*[' + nameAttribute + ']').each(function(key, element) {
			var name = $(element).attr(nameAttribute);
			if (name) {
				if ($(element).is(':radio')) {
					if ($(element).is(':checked')) {
						output[name] = $(element).val();
					}
				} else {
					output[name] = $(element).val();
				}
			}
		});
		return output;
	}

	/**
	 * Validate the form fields.
	 * 
	 * @return boolean
	 */
	Form.validate = function(name, successCallback, failureCallback) {
		var self = this;
		
		return Form.Validation.validate(name,
			function(valid) {
				for (var i = 0; i < valid.length; i++) {
					var elementId = valid[i];
					$('#' + elementId).removeClass('mage-error');
					$('#' + elementId + '-error').remove();

				}
				successCallback();
			},
			function(errors, valid) {
				for (var i = 0; i < valid.length; i++) {
					var elementId = valid[i];
					$('#' + elementId).removeClass('mage-error');
					$('#' + elementId + '-error').remove();

				}
				$.each(errors, function(elementId, error) {
					$('#' + elementId + '-error').remove();
					$('#' + elementId).parents('.field').last().append(self.fieldErrorTmpl({
						id: elementId,
						message: error
					}));
				});
				failureCallback();
			}
		);
	}

	return Form;
});