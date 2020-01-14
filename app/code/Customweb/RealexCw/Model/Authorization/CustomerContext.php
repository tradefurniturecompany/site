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

namespace Customweb\RealexCw\Model\Authorization;

/**
 * @method string getContextId()
 * @method \Customweb\RealexCw\Model\Authorization\CustomerContext setContextId(string $value)
 * @method string getCustomerId()
 * @method \Customweb\RealexCw\Model\Authorization\CustomerContext setCustomerId(string $value)
 * @method array getValues()
 * @method \Customweb\RealexCw\Model\Authorization\CustomerContext setValues(array $value)
 * @method int getVersionNumber()
 * @method \Customweb\RealexCw\Model\Authorization\CustomerContext setVersionNumber(int $value)
 */
class CustomerContext extends \Magento\Framework\Model\AbstractModel implements \Customweb_Payment_Authorization_IPaymentCustomerContext
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_customer_context';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'customer_context';

	/**
	 * @var @\Customweb\RealexCw\Model\Authorization\CustomerContextFactory
	 */
	protected $_factory;

	/**
	 * @var \Customweb_Payment_Authorization_DefaultPaymentCustomerContext
	 */
	private $context;

	/**
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Customweb\RealexCw\Model\Authorization\CustomerContextFactory $factory
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Customweb\RealexCw\Model\Authorization\CustomerContextFactory $factory,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
	) {
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);

		$this->_factory = $factory;
	}

    protected function _construct()
    {
        $this->_init('Customweb\RealexCw\Model\ResourceModel\Authorization\CustomerContext');

        $this->context = new \Customweb_Payment_Authorization_DefaultPaymentCustomerContext([]);
    }

    protected function _afterLoad()
    {
    	$this->context = new \Customweb_Payment_Authorization_DefaultPaymentCustomerContext($this->getValues());
    	return parent::_afterLoad();
    }

    public function beforeSave()
    {
    	if ($this->getContextId() !== null) {
    		$currentContext = $this->_factory->create()->load($this->getId());
    		$newMap = $this->context->applyUpdatesOnMapAndReset($currentContext->getMap());
    		$this->setValues($newMap);
    	} else {
    		$this->setValues($this->context->getMap());
    	}

    	parent::beforeSave();
    }

    public function afterSave()
    {
    	parent::afterSave();

    	$this->context = new \Customweb_Payment_Authorization_DefaultPaymentCustomerContext($this->getValues());
    }

    public function save()
    {
    	if ($this->getCustomerId()) {
    		return parent::save();
    	} else {
	    	return $this;
    	}
    }

    /**
     * Load entry by its customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
    	$this->load($customerId, 'customer_id');
    	$this->setCustomerId($customerId);
    	return $this;
    }

    public function updateMap(array $update) {
    	$this->setHasDataChanges(true);
    	return $this->context->updateMap($update);
    }

    public function getMap() {
    	return $this->context->getMap();
    }

    /**
     * @return \Customweb_Payment_Authorization_DefaultPaymentCustomerContext
     */
    public function getContext(){
    	return $this->context;
    }

    /**
     * @param \Customweb_Payment_Authorization_DefaultPaymentCustomerContext $context
     * @return \Customweb\RealexCw\Model\Authorization\CustomerContext
     */
    public function setContext(\Customweb_Payment_Authorization_DefaultPaymentCustomerContext $context){
    	$this->context = $context;
    	return $this;
    }
}