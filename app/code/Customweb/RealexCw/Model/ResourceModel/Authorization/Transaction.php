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

namespace Customweb\RealexCw\Model\ResourceModel\Authorization;

class Transaction extends \Customweb\RealexCw\Model\ResourceModel\AbstractVersionedModel
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_transaction_resource';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'resource';

	protected $_serializableFields = [
		'transaction_object' => [null, null]
	];

	/**
	 * @var \Magento\SalesSequence\Model\Manager
	 */
	protected $_sequenceManager;

	/**
	 * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
	 * @param \Magento\SalesSequence\Model\Manager $sequenceManager
	 * @param string $connectionName
	 */
	public function __construct(
			\Magento\Framework\Model\ResourceModel\Db\Context $context,
			\Magento\SalesSequence\Model\Manager $sequenceManager,
			$connectionName = null
	){
		parent::__construct($context, $connectionName);
		$this->_sequenceManager = $sequenceManager;
	}

	
	public function unserializeTransactionObject(\Customweb\RealexCw\Model\Authorization\Transaction $transaction){
		$this->_unserializeField($transaction, 'transaction_object', $transaction->getData('transaction_object'));
	}
	
	/**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customweb_realexcw_transaction', 'entity_id');
    }

    /**
     * Perform actions before object save, calculate next sequence value for increment Id
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
    	/** @var \Magento\Sales\Model\AbstractModel $object */
    	if ($object instanceof \Magento\Sales\Model\EntityInterface && $object->getIncrementId() == null) {
    		$object->setIncrementId(
    				$this->_sequenceManager->getSequence(
    						$object->getEntityType(),
    						$object->getStore()->getGroup()->getDefaultStoreId()
    				)->getNextValue()
    		);
    	}
    	parent::_beforeSave($object);
    	return $this;
    }
}