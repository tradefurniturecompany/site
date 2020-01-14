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

namespace Customweb\RealexCw\Api\Data;

/**
 * Transaction interface.
 *
 * A Realex transaction is an entity that holds information about the payment.
 * @api
 */
interface TransactionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case.
	 */
	/*
	 * Alias active.
	 */
	const ALIAS_ACTIVE = 'alias_active';
	/*
	 * Alias for display.
	 */
	const ALIAS_FOR_DISPLAY = 'alias_for_display';
	/*
	 * Authorization amount.
	 */
	const AUTHORIZATION_AMOUNT = 'authorization_amount';
	/*
	 * Authorization status.
	 */
	const AUTHORIZATION_STATUS = 'authorization_status';
	/*
	 * Authorization type.
	 */
	const AUTHORIZATION_TYPE = 'authorization_type';
	/*
	 * Created-at timestamp.
	 */
	const CREATED_AT = 'created_at';
	/*
	 * Currency.
	 */
	const CURRENCY = 'currency';
	/*
	 * Customer ID.
	 */
	const CUSTOMER_ID = 'customer_id';
	/*
	 * Entity ID.
	 */
	const ENTITY_ID = 'entity_id';
	/*
	 * Execute-update-on timestamp.
	 */
	const EXECUTE_UPDATE_ON = 'execute_update_on';
	/*
	 * Increment ID.
	 */
	const INCREMENT_ID = 'increment_id';
	/*
	 * Live transaction.
	 */
	const LIVE_TRANSACTION = 'live_transaction';
	/*
	 * Order ID.
	 */
	const ORDER_ID = 'order_id';
	/*
	 * Order Payment ID.
	 */
	const ORDER_PAYMENT_ID = 'order_payment_id';
	/*
	 * Paid.
	 */
	const PAID = 'paid';
	/*
	 * Payment ID.
	 */
	const PAYMENT_ID = 'payment_id';
	/*
	 * Payment method.
	 */
	const PAYMENT_METHOD = 'payment_method';
	/*
	 * Send Email.
	 */
	const SEND_EMAIL = 'send_email';
	/*
	 * Store ID.
	 */
	const STORE_ID = 'store_id';
	/*
	 * Transaction Data.
	 */
	const TRANSACTION_DATA = 'transaction_data';
	/*
	 * Transaction External ID.
	 */
	const TRANSACTION_EXTERNAL_ID = 'transaction_external_id';
	/*
	 * Updated-at timestamp.
	 */
	const UPDATED_AT = 'updated_at';

	/**
	 * Gets true if the transaction's alias is active.
	 *
	 * @return boolean Alias active.
	 */
	public function isAliasActive();

	/**
	 * Gets the alias for display for the transaction.
	 *
	 * @return string|null Alias for display.
	 */
	public function getAliasForDisplay();

	/**
	 * Gets the authorization amount for the transaction.
	 *
	 * @return float Authorization amount.
	 */
	public function getAuthorizationAmount();

	/**
	 * Gets the authorization status for the transaction.
	 *
	 * @return string Authorization status.
	 */
	public function getAuthorizationStatus();

	/**
	 * Gets the authorization type for the transaction.
	 *
	 * @return string Authorization type.
	 */
	public function getAuthorizationType();

	/**
	 * Gets the created-at timestamp for the transaction.
	 *
	 * @return string|null Created-at timestamp.
	 */
	public function getCreatedAt();

	/**
	 * Gets the currency code for the transaction.
	 *
	 * @return string Currency code.
	 */
	public function getCurrency();

	/**
	 * Gets the customer ID for the transaction.
	 *
	 * @return int Customer ID.
	 */
	public function getCustomerId();

	/**
	 * Gets the ID for the transaction.
	 *
	 * @return int Transaction ID.
	 */
	public function getEntityId();

	/**
	 * Gets the execture-update-on timestamp for the transaction.
	 *
	 * @return boolean Execute-update-on timestamp
	 */
	public function getExecuteUpdateOn();

	/**
	 * Gets the increment ID for the transaction.
	 *
	 * @return string Increment ID.
	 */
	public function getIncrementId();

	/**
	 * Gets true if the transaction is live.
	 *
	 * @return boolean Live
	 */
	public function isLiveTransaction();

	/**
	 * Gets the order ID for the transaction.
	 *
	 * @return int Order ID.
	 */
	public function getOrderId();

	/**
	 * Gets the order payment ID for the transaction.
	 *
	 * @return int Order payment ID.
	 */
	public function getOrderPaymentId();

	/**
	 * Gets true if the transaction is paid.
	 *
	 * @return boolean Paid
	 */
	public function isPaid();

	/**
	 * Gets the payment ID for the transaction.
	 *
	 * @return string|null Payment ID.
	 */
	public function getPaymentId();

	/**
	 * Gets the payment method for the transaction.
	 *
	 * @return string Payment method.
	 */
	public function getPaymentMethod();

	/**
	 * Gets true if the email should be sent.
	 *
	 * @return boolean Send Email
	 */
	public function isSendEmail();

	/**
	 * Gets the store ID for the transaction.
	 *
	 * @return int Store ID.
	 */
	public function getStoreId();

	/**
	 * Gets the transaction data for the transaction.
	 *
	 * @return \Customweb\RealexCw\Api\Data\TransactionDataInterface[]|null Transaction data.
	 */
	public function getTransactionData();

	/**
	 * Gets the external ID for the transaction.
	 *
	 * @return string External ID.
	 */
	public function getTransactionExternalId();

	/**
	 * Gets the updated-at timestamp for the transaction.
	 *
	 * @return string|null Updated-at timestamp.
	 */
	public function getUpdatedAt();

}