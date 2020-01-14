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

namespace Customweb\RealexCw\Block\ExternalCheckout;

class AbstractExternalCheckout extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Customweb\RealexCw\Model\ExternalCheckout\Context
	 */
	private $context;

	/**
	 * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
	 */
	public function getContext()
	{
		if (!($this->context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Exception('The context has not been set.');
		}
		return $this->context;
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 * @return \Customweb\RealexCw\Block\ExternalCheckout\AbstractExternalCheckout
	 */
	public function setContext(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$this->context = $context;
		return $this;
	}
}