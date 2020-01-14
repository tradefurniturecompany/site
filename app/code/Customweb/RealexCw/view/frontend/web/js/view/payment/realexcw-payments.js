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
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
], function(
	Component,
	rendererList
) {
	'use strict';
	
	rendererList.push(
			{
			    type: 'realexcw_creditcard',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_creditcard-method'
			},
			{
			    type: 'realexcw_visa',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_visa-method'
			},
			{
			    type: 'realexcw_mastercard',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_mastercard-method'
			},
			{
			    type: 'realexcw_americanexpress',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_americanexpress-method'
			},
			{
			    type: 'realexcw_lasercard',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_lasercard-method'
			},
			{
			    type: 'realexcw_diners',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_diners-method'
			},
			{
			    type: 'realexcw_directdebits',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_directdebits-method'
			},
			{
			    type: 'realexcw_giropay',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_giropay-method'
			},
			{
			    type: 'realexcw_paypal',
			    component: 'Customweb_RealexCw/js/view/payment/method-renderer/realexcw_paypal-method'
			});
	return Component.extend({});
});