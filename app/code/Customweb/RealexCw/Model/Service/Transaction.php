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

namespace Customweb\RealexCw\Model\Service;

use \Customweb\RealexCw\Api\Data\TransactionInterface;

class Transaction  extends \Magento\Framework\DataObject implements TransactionInterface
{
	public function isAliasActive()
	{
		return $this->getData(TransactionInterface::ALIAS_ACTIVE);
	}

	public function getAliasForDisplay()
	{
		return $this->getData(TransactionInterface::ALIAS_FOR_DISPLAY);
	}

	public function getAuthorizationAmount()
	{
		return $this->getData(TransactionInterface::AUTHORIZATION_AMOUNT);
	}

	public function getAuthorizationStatus()
	{
		return $this->getData(TransactionInterface::AUTHORIZATION_STATUS);
	}

	public function getAuthorizationType()
	{
		return $this->getData(TransactionInterface::AUTHORIZATION_TYPE);
	}

	public function getCreatedAt()
	{
		return $this->getData(TransactionInterface::CREATED_AT);
	}

	public function getCurrency()
	{
		return $this->getData(TransactionInterface::CURRENCY);
	}

	public function getCustomerId()
	{
		return $this->getData(TransactionInterface::CUSTOMER_ID);
	}

	public function getEntityId() {
		return $this->getData(TransactionInterface::ENTITY_ID);
	}

	public function getExecuteUpdateOn()
	{
		return $this->getData(TransactionInterface::EXECUTE_UPDATE_ON);
	}

	public function getIncrementId()
	{
		return $this->getData(TransactionInterface::INCREMENT_ID);
	}

	public function isLiveTransaction()
	{
		return $this->getData(TransactionInterface::LIVE_TRANSACTION);
	}

	public function getOrderId()
	{
		return $this->getData(TransactionInterface::ORDER_ID);
	}

	public function getOrderPaymentId()
	{
		return $this->getData(TransactionInterface::ORDER_PAYMENT_ID);
	}

	public function isPaid()
	{
		return $this->getData(TransactionInterface::PAID);
	}

	public function getPaymentId()
	{
		return $this->getData(TransactionInterface::PAYMENT_ID);
	}

	public function getPaymentMethod()
	{
		return $this->getData(TransactionInterface::PAYMENT_METHOD);
	}

	public function isSendEmail()
	{
		return $this->getData(TransactionInterface::SEND_EMAIL);
	}

	public function getStoreId()
	{
		return $this->getData(TransactionInterface::STORE_ID);
	}

	public function getTransactionExternalId()
	{
		return $this->getData(TransactionInterface::TRANSACTION_EXTERNAL_ID);
	}

	public function getUpdatedAt()
	{
		return $this->getData(TransactionInterface::UPDATED_AT);
	}

	public function getTransactionData()
	{
		return $this->getData(TransactionInterface::TRANSACTION_DATA);
	}

	public function setAliasActive($value)
	{
		$this->setData(TransactionInterface::ALIAS_ACTIVE, $value);
		return $this;
	}

	public function setAliasForDisplay($value)
	{
		$this->setData(TransactionInterface::ALIAS_FOR_DISPLAY, $value);
		return $this;
	}

	public function setAuthorizationAmount($value)
	{
		$this->setData(TransactionInterface::AUTHORIZATION_AMOUNT, $value);
		return $this;
	}

	public function setAuthorizationStatus($value)
	{
		$this->setData(TransactionInterface::AUTHORIZATION_STATUS, $value);
		return $this;
	}

	public function setAuthorizationType($value)
	{
		$this->setData(TransactionInterface::AUTHORIZATION_TYPE, $value);
		return $this;
	}

	public function setCreatedAt($value)
	{
		$this->setData(TransactionInterface::CREATED_AT, $value);
		return $this;
	}

	public function setCurrency($value)
	{
		$this->setData(TransactionInterface::CURRENCY, $value);
		return $this;
	}

	public function setCustomerId($value)
	{
		$this->setData(TransactionInterface::CUSTOMER_ID, $value);
		return $this;
	}

	public function setEntityId($value)
	{
		$this->setData(TransactionInterface::ENTITY_ID, $value);
		return $this;
	}

	public function setExecuteUpdateOn($value)
	{
		$this->setData(TransactionInterface::EXECUTE_UPDATE_ON, $value);
		return $this;
	}

	public function setIncrementId($value)
	{
		$this->setData(TransactionInterface::INCREMENT_ID, $value);
		return $this;
	}

	public function setLiveTransaction($value)
	{
		$this->setData(TransactionInterface::LIVE_TRANSACTION, $value);
		return $this;
	}

	public function setOrderId($value)
	{
		$this->setData(TransactionInterface::ORDER_ID, $value);
		return $this;
	}

	public function setOrderPaymentId($value)
	{
		$this->setData(TransactionInterface::ORDER_PAYMENT_ID, $value);
		return $this;
	}

	public function setPaid($value)
	{
		$this->setData(TransactionInterface::PAID, $value);
		return $this;
	}

	public function setPaymentId($value)
	{
		$this->setData(TransactionInterface::PAYMENT_ID, $value);
		return $this;
	}

	public function setPaymentMethod($value)
	{
		$this->setData(TransactionInterface::PAYMENT_METHOD, $value);
		return $this;
	}

	public function setSendEmail($value)
	{
		$this->setData(TransactionInterface::SEND_EMAIL, $value);
		return $this;
	}

	public function setStoreId($value)
	{
		$this->setData(TransactionInterface::STORE_ID, $value);
		return $this;
	}

	public function setTransactionData($value)
	{
		$this->setData(TransactionInterface::TRANSACTION_DATA, $value);
		return $this;
	}

	public function setTransactionExternalId($value)
	{
		$this->setData(TransactionInterface::TRANSACTION_EXTERNAL_ID, $value);
		return $this;
	}

	public function setUpdatedAt($value)
	{
		$this->setData(TransactionInterface::UPDATED_AT, $value);
		return $this;
	}
}