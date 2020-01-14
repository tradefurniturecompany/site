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

class StorageBackend implements \Customweb_Storage_IBackend
{
	/**
	 * Storage model factory
	 *
	 * @var \Customweb\RealexCw\Model\StorageFactory
	 */
	protected $_storageFactory;

	public function __construct(
			\Customweb\RealexCw\Model\StorageFactory $storageFactory
	) {
		$this->_storageFactory = $storageFactory;
	}

	public function lock($space, $key, $type) {
		$entity = $this->createEntity();
		$entity->getResource()->beginTransaction();
		$entity->loadBySpaceAndKey($space, $key);
		if ($entity == null || !$entity->getId()) {
			$this->write($space, $key, null);
			$entity->loadBySpaceAndKey($space, $key);
		}
		if ($type == self::EXCLUSIVE_LOCK) {
			$entity->save();
		}
	}

	public function unlock($space, $key) {
		$this->createEntity()->getResource()->commit();
	}

	public function read($space, $key) {
		$entity = $this->createEntity();
		$entity->loadBySpaceAndKey($space, $key);
		if ($entity != null && $entity->getId()) {
			return \Customweb_Core_Util_Serialization::unserialize($entity->getKeyValue());
		} else {
			return null;
		}
	}

	public function write($space, $key, $value) {
		$entity = $this->createEntity();
		$entity->loadBySpaceAndKey($space, $key);
		$entity->setKeySpace($space);
		$entity->setKeyName($key);
		$entity->setKeyValue(\Customweb_Core_Util_Serialization::serialize($value));
		$entity->save();
	}

	public function remove($space, $key) {
		$entity = $this->createEntity();
		$entity->loadBySpaceAndKey($space, $key);
		if ($entity != null && $entity->getId()) {
			$entity->delete();
		}
	}

	/**
	 * @return \Customweb\RealexCw\Model\Storage
	 */
	private function createEntity() {
		return $this->_storageFactory->create();
	}
}