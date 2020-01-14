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

namespace Customweb\RealexCw\Model\ResourceModel\ExternalCheckout;

class Context extends \Customweb\RealexCw\Model\ResourceModel\AbstractVersionedModel
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_external_checkout_context_resource';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'resource';

	protected $_serializableFields = [
		'invoice_items' => [null, []],
		'billing_address' => [null, null],
		'shipping_address' => [null, null],
		'provider_data' => [null, []]
	];

	/**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customweb_realexcw_external_checkout_context', 'context_id');
    }

    /**
     * Load reusable data by specified quote id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $quoteId
     * @return array
     */
    public function loadReusableByQuoteId(\Magento\Framework\Model\AbstractModel $object, $quoteId)
    {
    	$read = $this->getConnection();
    	if ($read) {
	    	$select = $read->select()->from($this->getMainTable())->where('quote_id=:quote_id')->where('state=:state');
	    	$binds = [
	    		'quote_id'	=> $quoteId,
	    		'state'		=> \Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING
	    	];
			$data = $read->fetchRow($select, $binds);

			if ($data) {
				$object->setData($data);
			}
    	}

    	$this->unserializeFields($object);
    	$this->_afterLoad($object);

    	return $this;
    }
}