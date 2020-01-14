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

namespace Customweb\RealexCw\Controller\Endpoint;

class Index
	extends \Customweb\RealexCw\Controller\Endpoint
	/**
	 * 2019-10-10 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Realex Payments in Magento 2.3:
	 * «Your transaction has been successful but there was a problem connecting back to the merchant's web site»":
	 * https://github.com/tradefurniturecompany/core/issues/16
	 */
	implements \Magento\Framework\App\CsrfAwareActionInterface
{
	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @var \Customweb\RealexCw\Model\Adapter\Endpoint
	 */
	protected $_endpointAdapter;

	/**
	 * 2019-10-10 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Realex Payments in Magento 2.3:
	 * «Your transaction has been successful but there was a problem connecting back to the merchant's web site»":
	 * https://github.com/tradefurniturecompany/core/issues/16
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return \Magento\Framework\App\Request\InvalidRequestException|null
	 */
    function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }

	/**
	 * 2019-10-10 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Realex Payments in Magento 2.3:
	 * «Your transaction has been successful but there was a problem connecting back to the merchant's web site»":
	 * https://github.com/tradefurniturecompany/core/issues/16
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return \Magento\Framework\App\Request\InvalidRequestException|null
	 */
    function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Customweb\RealexCw\Model\Adapter\Endpoint $endpointAdapter
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Customweb\RealexCw\Model\Adapter\Endpoint $endpointAdapter
	) {
		parent::__construct($context);
		$this->_container = $container;
		$this->_endpointAdapter = $endpointAdapter;
	}

	public function execute()
	{
		$packages = array(
			0 => 'Customweb_Realex',
 			1 => 'Customweb_Payment_Authorization',
 		);
		$dispatcher = new \Customweb_Payment_Endpoint_Dispatcher($this->_endpointAdapter, $this->_container, $packages);
		$response = $dispatcher->dispatch(\Customweb_Core_Http_ContextRequest::getInstance());
		$wrapper = new \Customweb_Core_Http_Response($response);
		$wrapper->send();
		die();
	}

}