<?php
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

namespace Customweb\RealexCw\Api\Data;

/**
 * Authorization data interface.
 * @api
 */
interface AuthorizationDataInterface {

	/**
	 * Gets the form action URL.
	 *
	 * @return string
	 */
	public function getFormActionUrl();

	/**
	 * Gets the hidden form fields.
	 *
	 * @return \Customweb\RealexCw\Api\Data\AuthorizationFormFieldInterface[]
	 */
	public function getHiddenFormFields();

	/**
	 * Gets the AJAX file URL.
	 *
	 * @return string
	 */
	public function getAjaxFileUrl();

	/**
	 * Gets the javascript callback function.
	 *
	 * @return string
	 */
	public function getJavaScriptCallbackFunction();

	/**
	 * Gets the redirection URL.
	 *
	 * @return string
	 */
	public function getRedirectionUrl();

}