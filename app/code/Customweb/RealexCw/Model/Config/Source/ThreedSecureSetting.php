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

namespace Customweb\RealexCw\Model\Config\Source;

class ThreedSecureSetting implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => 'force_3d', 'label' => __('Allow only 3DSecure transactions (Very strict)')],
			['value' => 'accept_unenrolled', 'label' => __('Force 3DSecure transaction, if customer is enrolled for 3DSecure. If not enrolled go on without 3DSecure.')],
			['value' => 'no_3d', 'label' => __('Never use 3DSecure')],
		];
	}
}
