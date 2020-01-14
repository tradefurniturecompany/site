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
	 * Alias Class
	 * 
	 * @param string formElement
	 * @param object updateData
	 * @param function updateCallback
	 * @param string updateUrl
	 */
	var Alias = function(formElement, updateData, updateCallback, updateUrl) {
		/**
		 * @return void
		 */
		this.attachListeners = function() {
			$(document).on('change', formElement + ' select[name="alias"]', function() {
				updateData['alias'] = $(formElement + ' select[name="alias"]').val();
				$.ajax({
					url: updateUrl,
					type: 'POST',
					data: updateData,
					global: true
				}).done(
					function(response) {
						if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
							if (typeof updateCallback == 'function') {
								updateCallback(response);
							}
						}
					}
				);
			});
		}
	}

	return Alias;
});