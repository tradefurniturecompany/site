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

abstract class AbstractVersionedModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Name of the version number field
	 *
	 * @var string
	 */
	protected $_versionNumberFieldName = 'version_number';

	/**
	 * Get name of the version number field
	 *
	 * @return string
	 */
	public function getVersionNumberFieldName()
	{
		if (empty($this->_versionNumberFieldName)) {
			throw new \Exception(__('Empty version number field name'));
		}
		return $this->_versionNumberFieldName;
	}

	public function save(\Magento\Framework\Model\AbstractModel $object)
	{
		try {
			return parent::save($object);
		} catch (\Customweb\RealexCw\Model\Exception\OptimisticLockingException $e) {
			// Loads the model's latest version from the database in case an optimistic locking exception occurred.
			$this->load($object, $object->getId());
			throw $e;
		}
	}

	protected function updateObject(\Magento\Framework\Model\AbstractModel $object)
	{
		$condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $object->getId());

		$nextVersion = null;
		$currentVersion = $object->getData($this->getVersionNumberFieldName());
		if($currentVersion !== null){
			$nextVersion = $currentVersion + 1;
			$condition .= $this->getConnection()->quoteInto(' AND '.$this->getVersionNumberFieldName().'=?', $currentVersion);
		} else {
			$nextVersion = 1;
		}

		/**
		 * Not auto increment primary key support
		*/
		if ($this->_isPkAutoIncrement) {
			$data = $this->prepareDataForUpdate($object);
			$data[$this->getVersionNumberFieldName()] = $nextVersion;
			if (!empty($data)) {
				$rowAffected = $this->getConnection()->update($this->getMainTable(), $data, $condition);
				if($rowAffected == 0) {
					throw new \Customweb\RealexCw\Model\Exception\OptimisticLockingException(get_class($object), $object->getId());
				}
				$object->setVersionNumber($nextVersion);
			}
		} else {
			$select = $this->getConnection()->select()->from(
					$this->getMainTable(),
					[$this->getIdFieldName()]
			)->where(
					$condition
			);
			if ($this->getConnection()->fetchOne($select) !== false) {
				$data = $this->prepareDataForUpdate($object);
				$data[$this->getVersionNumberFieldName()] = $nextVersion;
				if (!empty($data)) {
					$rowAffected = $this->getConnection()->update($this->getMainTable(), $data, $condition);
					if($rowAffected == 0) {
						throw new \Customweb\RealexCw\Model\Exception\OptimisticLockingException(get_class($object), $object->getId());
					}
					$object->setData($this->getVersionNumberFieldName(), $nextVersion);
				}
			} else {
				$object->setData($this->getVersionNumberFieldName(), 1);
				$this->getConnection()->insert(
						$this->getMainTable(),
						$this->_prepareDataForSave($object)
				);
			}
		}
	}

	protected function _serializeField(\Magento\Framework\DataObject $object, $field, $defaultValue = null, $unsetEmpty = false)
	{
		$value = $object->getData($field);
		if (empty($value) && $unsetEmpty) {
			$object->unsetData($field);
		} else {
			$object->setData($field, $this->serialize($value ?: $defaultValue));
		}

		return $this;
	}

	protected function _unserializeField(\Magento\Framework\DataObject $object, $field, $defaultValue = null)
	{
		$value = $object->getData($field);
		if ($value) {
			$value = $this->unserialize($object->getData($field));
			if (empty($value)) {
				$object->setData($field, $defaultValue);
			} else {
				$object->setData($field, $value);
			}
		} else {
			$object->setData($field, $defaultValue);
		}
	}

	private function serialize($data)
	{
		return base64_encode(serialize($data));
	}

	private function unserialize($string)
	{
		$decoded = base64_decode($string);
		if ($decoded == 'null') {
			return null;
		} else {
			return unserialize($decoded);
		}
	}
}