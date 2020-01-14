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

namespace Customweb\RealexCw\Model\ResourceModel;

class Storage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_storage_resource';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'resource';

	/**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customweb_realexcw_storage', 'key_id');
    }

    /**
     * Load data by specified space and key
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $space
     * @param string $key
     * @return array
     */
    public function loadBySpaceAndKey(\Magento\Framework\Model\AbstractModel $object, $space, $key)
    {
    	$read = $this->getConnection();
    	if ($read) {
    		$select = $read->select()->from($this->getMainTable())->where('key_space=:space')->where('key_name=:key');
	    	$binds = [
	    		'space'	=> $space,
	    		'key'	=> $key
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