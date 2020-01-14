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

namespace Customweb\RealexCw\Model\Adapter;

class Endpoint extends \Customweb_Payment_Endpoint_AbstractAdapter
{
	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_frontendUrlBuilder;

	/**
     * @var \Customweb\RealexCw\Model\Configuration
     */
    private $_configuration;

    /**
     * @param \Magento\Framework\UrlInterface $frontendUrlBuilder
     * @param \Customweb\RealexCw\Model\Configuration $configuration
     */
	public function __construct(
			\Magento\Framework\UrlInterface $frontendUrlBuilder,
			\Customweb\RealexCw\Model\Configuration $configuration
	) {
		$this->_frontendUrlBuilder = $frontendUrlBuilder;
		$this->_configuration = $configuration;
	}

	protected function getBaseUrl() {
		return $this->_frontendUrlBuilder->setScope($this->_configuration->getStore())->getUrl('realexcw/endpoint/index', ['_secure' => true, '_nosid' => true]);
	}

	protected function getControllerQueryKey() {
		return 'c';
	}

	protected function getActionQueryKey() {
		return 'a';
	}

	public function getFormRenderer() {
		return new \Customweb\RealexCw\Model\Renderer\FrontendForm();
	}
}