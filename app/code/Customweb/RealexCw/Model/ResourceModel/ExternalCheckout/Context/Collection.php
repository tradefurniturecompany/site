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

namespace Customweb\RealexCw\Model\ResourceModel\ExternalCheckout\Context;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_external_checkout_context_collection';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'external_checkout_context_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Customweb\RealexCw\Model\ExternalCheckout\Context', 'Customweb\RealexCw\Model\ResourceModel\ExternalCheckout\Context');
	}
}