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
 * Authorization form field interface.
 * @api
 */
interface AuthorizationFormFieldInterface {

	/**
	 * Gets the field's key.
	 *
	 * @return string
	 */
	public function getKey();

	/**
	 * Gets the field's value.
	 *
	 * @return string
	 */
	public function getValue();

	/**
	 * Sets the field's key.
	 *
	 * @param string $key
	 * @return $this
	 */
	public function setKey($key);

	/**
	 * Sets the field's value.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value);

}