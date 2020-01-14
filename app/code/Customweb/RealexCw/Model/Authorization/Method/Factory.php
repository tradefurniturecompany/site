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

namespace Customweb\RealexCw\Model\Authorization\Method;

class Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Customweb\RealexCw\Model\Authorization\Method\Context\Factory
     */
    protected $_contextFactory;

    /**
     * @var \Customweb\RealexCw\Model\DependencyContainer
     */
    protected $_container;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Customweb\RealexCw\Model\Authorization\Method\Context\Factory $contextFactory
     * @param \Customweb\RealexCw\Model\DependencyContainer $container
     */
    public function __construct(
    		\Magento\Framework\ObjectManagerInterface $objectManager,
    		\Magento\Store\Model\StoreManager $storeManager,
    		\Customweb\RealexCw\Model\Authorization\Method\Context\Factory $contextFactory,
    		\Customweb\RealexCw\Model\DependencyContainer $container
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_contextFactory = $contextFactory;
        $this->_container = $container;
    }

    /**
     * @param \Customweb\RealexCw\Model\Authorization\Method\Context\IContext $context
     * @return \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod
     */
    public function create(\Customweb\RealexCw\Model\Authorization\Method\Context\IContext $context)
    {
    	if ($context->isMoto()) {
    		return $this->createMoto($context);
    	}
    	return $this->_objectManager->create($this->getClass($this->getAdapter($context->getOrderContext())), ['context' => $context]);
    }

    /**
     * @param \Customweb\RealexCw\Model\Authorization\Method\Context\IContext $context
     * @return \Customweb\RealexCw\Model\Authorization\Method\Moto
     */
    public function createMoto(\Customweb\RealexCw\Model\Authorization\Method\Context\IContext $context)
    {
    	return $this->_objectManager->create('\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Moto', ['context' => $context]);
    }

    /**
     * @return \Customweb\RealexCw\Model\Authorization\Method\Context\Factory
     */
    public function getContextFactory()
    {
    	return $this->_contextFactory;
    }

    /**
     * @param \Customweb_Payment_Authorization_IOrderContext $orderContext
     * @return \Customweb_Payment_Authorization_IAdapter
     */
    private function getAdapter($orderContext)
    {
    	/* @var $adapterFactory \Customweb_Payment_Authorization_IAdapterFactory */
    	$adapterFactory = $this->_container->getBean('Customweb_Payment_Authorization_IAdapterFactory');
    	return $adapterFactory->getAuthorizationAdapterByContext($orderContext);
    }

    /**
     * @param \Customweb_Payment_Authorization_IAdapter $adapter
     * @return string
     * @throws \Exception
     */
    private function getClass(\Customweb_Payment_Authorization_IAdapter $adapter)
    {
       	switch ($adapter->getAuthorizationMethodName()) {
    		case \Customweb_Payment_Authorization_PaymentPage_IAdapter::AUTHORIZATION_METHOD_NAME:
    			return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\PaymentPage';
    		case \Customweb_Payment_Authorization_Hidden_IAdapter::AUTHORIZATION_METHOD_NAME:
    			return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Hidden';
    		case \Customweb_Payment_Authorization_Ajax_IAdapter::AUTHORIZATION_METHOD_NAME:
    			return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Ajax';
  			case \Customweb_Payment_Authorization_Iframe_IAdapter::AUTHORIZATION_METHOD_NAME:
  				return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Iframe';
   			case \Customweb_Payment_Authorization_Server_IAdapter::AUTHORIZATION_METHOD_NAME:
    			return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Server';
    		case \Customweb_Payment_Authorization_Widget_IAdapter::AUTHORIZATION_METHOD_NAME:
    			return '\\Customweb\\RealexCw\\Model\\Authorization\\Method\\Widget';
    		default:
    			throw new \Exception(\Customweb_Core_String::_("There is no authorization method adapter for '!name'.")->format(['!name' => $adapter->getAuthorizationMethodName()]));
    	}
    }
}
