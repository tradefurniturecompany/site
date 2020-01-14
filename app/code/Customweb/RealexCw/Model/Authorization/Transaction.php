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

use \Customweb\RealexCw\Api\Data\TransactionInterface;
use Magento\Customer\Model\Customer;

/**
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setEntityId(int $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setTransactionExternalId(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setOrderId(int $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setOrderIncrementId(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setOrderPaymentId(int $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setAliasForDisplay(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setAliasActive(boolean $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setPaymentMethod(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setTransactionObject(\Customweb_Payment_Authorization_ITransaction $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setAuthorizationType(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setCustomerId(int $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setUpdatedOn(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setCreatedOn(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setPaymentId(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setExecuteUpdateOn(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setAuthorizationAmount(float $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setAuthorizationStatus(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setPaid(boolean $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setLiveTransaction(boolean $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setCurrency(string $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setSendEmail(boolean $value)
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setStoreId(int $value)
 * @method int getVersionNumber()
 * @method \Customweb\RealexCw\Model\Authorization\Transaction setVersionNumber(int $value)
 */
class Transaction extends \Magento\Framework\Model\AbstractModel implements \Magento\Sales\Model\EntityInterface, TransactionInterface
{
	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_transaction';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'transaction';

	/**
	 * Identifier for sequence
	 *
	 * @var string
	 */
	protected $_entityType = 'rexcw_trx';

	/**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $_orderFactory;

	/**
	 * @var \Magento\Sales\Model\Order\PaymentFactory
	 */
	protected $_orderPaymentFactory;

	/**
	 * @var \Magento\Quote\Model\QuoteFactory
	 */
	protected $_quoteFactory;

	/**
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Alias\Handler
	 */
	protected $_aliasHandler;

	/**
	 * @var \Customweb_Core_ILogger
	 */
	private $logger;

	/**
	 * @var \Magento\Sales\Model\Order
	 */
	private $cachedOrder;

	/**
	 * @var \Magento\Sales\Model\Order\Payment
	 */
	private $cachedOrderPayment;

	/**
	 * @var \Magento\Quote\Model\Quote
	 */
	private $cachedQuote;

	/**
	 * @var \Magento\Customer\Model\Customer
	 */
	private $cachedCustomer;

	/**
	 * @var boolean
	 */
	private $authorizationRequired = false;

	/**
	 * @var boolean
	 */
	private $cachedUncertainFlag = null;

	/**
	 * @var boolean
	 */
	private $cachedRefusingFlag = null;

	/**
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Sales\Model\OrderFactory $orderFactory
	 * @param \Magento\Sales\Model\Order\PaymentFactory $orderPaymentFactory
	 * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Customweb\RealexCw\Model\Alias\Handler $aliasHandler
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\Db $resourceCollection
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Sales\Model\OrderFactory $orderFactory,
			\Magento\Sales\Model\Order\PaymentFactory $orderPaymentFactory,
			\Magento\Quote\Model\QuoteFactory $quoteFactory,
			\Magento\Customer\Model\CustomerFactory $customerFactory,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			\Customweb\RealexCw\Model\Alias\Handler $aliasHandler,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
	) {
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);

		$this->_orderFactory = $orderFactory;
		$this->_orderPaymentFactory = $orderPaymentFactory;
		$this->_quoteFactory = $quoteFactory;
		$this->_customerFactory = $customerFactory;
		$this->_storeManager = $storeManager;
		$this->_container = $container;
		$this->_authorizationMethodFactory = $authorizationMethodFactory;
		$this->_aliasHandler = $aliasHandler;
		$this->logger = \Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	protected function _construct()
	{
		$this->_init('Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction');
	}

	protected function _afterLoad()
	{
		parent::_afterLoad();

		if ($this->getTransactionObject() !== null && $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			$this->cachedUncertainFlag = $this->getTransactionObject()->isAuthorizationUncertain();
			$this->cachedRefusingFlag = $this->getTransactionObject()->isCustomerRefusingToPay();
		}
	}

	public function save()
	{
		$this->setHasDataChanges(true);
		return parent::save();
	}

	public function beforeSave()
	{
		if (!$this->getId()) {
			if (!($this->getOrder() instanceof \Magento\Sales\Model\Order)) {
				throw new \Exception("The order has not been set.");
			}
			$this->setStoreId($this->getOrder()->getStoreId());
			$this->setOrderId($this->getOrder()->getId());
			$this->setOrderIncrementId($this->getOrder()->getIncrementId());
			$this->setOrderPaymentId($this->getOrder()->getPayment()->getId());
			$this->setSendEmail(true);
		}

		if ($this->getTransactionObject() !== null && $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			$this->authorizationRequired = $this->isAuthorizationRequired();

			$aliasForDisplay = $this->getTransactionObject()->getAliasForDisplay();
			if (!empty($aliasForDisplay)){
				$deleteSimilar = $this->getAliasForDisplay() == null;
				$this->setAliasForDisplay($aliasForDisplay);
				if ($deleteSimilar) {
					$this->_aliasHandler->removeSimilarAliases($this);
				}
			}
			// When the alias for display is empty and the alias was once set as active we deactivate it.
			$currentSetAlias = $this->getAliasForDisplay();
			if (empty($aliasForDisplay) && !empty($currentSetAlias)) {
				$this->setAliasActive(false);
			}

			$this->setAuthorizationType($this->getTransactionObject()->getAuthorizationMethod());
			$this->setPaymentMethod($this->getTransactionObject()->getPaymentMethod()->getCode());
			$this->setPaymentId($this->getTransactionObject()->getPaymentId());
			$this->setAuthorizationAmount($this->getTransactionObject()->getAuthorizationAmount());
			$this->setCurrency($this->getTransactionObject()->getCurrencyCode());
			$this->setExecuteUpdateOn($this->getTransactionObject()->getUpdateExecutionDate());
			$this->setAuthorizationStatus($this->getTransactionObject()->getAuthorizationStatus());
			$this->setTransactionExternalId($this->getTransactionObject()->getExternalTransactionId());
			$this->setLiveTransaction($this->getTransactionObject()->isLiveTransaction());

			$customerId = $this->getTransactionObject()->getTransactionContext()->getOrderContext()->getCustomerId();
			if ($customerId !== null) {
				$customer = $this->_customerFactory->create()->load($customerId);
				if ($customer instanceof Customer && $customer->getId()) {
					$this->setCustomerId($customerId);
				}
			}
			$this->setPaid($this->getTransactionObject()->isPaid());
		}

		parent::beforeSave();
	}

	public function afterSave()
	{
		parent::afterSave();

		if ($this->getTransactionObject() !== null && $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			$customerContext = $this->getTransactionObject()->getPaymentCustomerContext();
			if ($customerContext instanceof \Customweb\RealexCw\Model\Authorization\CustomerContext) {
				try {
					$customerContext->setCustomerId($this->getCustomerId());
					$customerContext->save();
				}
				catch(\Exception $e) {}
			}
		}

		if ($this->_registry->registry('initializeTransaction') !== false && $this->getTransactionObject() == null) {
			$context = $this->_authorizationMethodFactory->getContextFactory()->createOrder($this->getOrder());
			/* @var $adapter \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod */
			$adapter = $this->_authorizationMethodFactory->create($context);
			$adapter->initializeTransaction($this);
		}

		if ($this->authorizationRequired) {
			$this->authorize();
			$this->authorizationRequired = false;
		}

		if ($this->isReviewActionRequired()) {
			$this->registerReviewAction();
		}

		if ($this->isRefusalActionRequired()) {
			$this->handleRefusal();
		}

		if ($this->getTransactionObject() !== null && $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			$this->cachedUncertainFlag = $this->getTransactionObject()->isAuthorizationUncertain();
			$this->cachedRefusingFlag = $this->getTransactionObject()->isCustomerRefusingToPay();
		}
	}

	/**
	 * Load transaction entry by its increment id
	 *
	 * @param string $incrementId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function loadByIncrementId($incrementId)
	{
		return $this->load($incrementId, 'increment_id');
	}

	/**
	 * Load transaction entry by its payment id
	 *
	 * @param string $paymentId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function loadByPaymentId($paymentId)
	{
		return $this->load($paymentId, 'payment_id');
	}

	/**
	 * Load transaction entry by its transaction external id
	 *
	 * @param string $transactionExternalId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function loadByTransactionExternalId($transactionExternalId)
	{
		return $this->load($transactionExternalId, 'transaction_external_id');
	}

	/**
	 * Load transaction entry by its order id
	 *
	 * @param int $orderId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function loadByOrderId($orderId)
	{
		return $this->load($orderId, 'order_id');
	}

	/**
	 * Load transaction entry by its order payment id
	 *
	 * @param int $orderPaymentId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function loadByOrderPaymentId($orderPaymentId)
	{
		return $this->load($orderPaymentId, 'order_payment_id');
	}

	/**
	 * Retrieve the order the transaction was created for
	 *
	 * @return \Magento\Sales\Model\Order
	 */
	public function getOrder()
	{
		if (!($this->cachedOrder instanceof \Magento\Sales\Model\Order)) {
			$this->cachedOrder = $this->_orderFactory->create()->load($this->getOrderId());
		}
		return $this->cachedOrder;
	}

	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function setOrder(\Magento\Sales\Model\Order $order)
	{
		$this->cachedOrder = $order;
		return $this;
	}

	/**
	 * Return sequence identifier
	 *
	 * @return string
	 */
	public function getEntityType()
	{
		return $this->_entityType;
	}

	/**
	 * Set the increment id
	 *
	 * @param string $id
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function setIncrementId($id)
	{
		if ($this->getIncrementId() != null) {
			throw new \Exception('The increment id cannot be overwritten.');
		}
		return $this->setData('increment_id', $id);
	}

	/**
	 * Retrieve the order payment the transaction was created for
	 *
	 * @return \Magento\Sales\Model\Order\Payment
	 */
	public function getOrderPayment()
	{
		if (!($this->cachedOrderPayment instanceof \Magento\Sales\Model\Order\Payment)) {
			$this->cachedOrderPayment = $this->_orderPaymentFactory->create()->load($this->getOrderPaymentId())->setOrder($this->getOrder());
		}
		return $this->cachedOrderPayment;
	}

	/**
	 * Retrieve the quote
	 *
	 * @return \Magento\Quote\Model\Quote
	 */
	public function getQuote()
	{
		if (!($this->cachedQuote instanceof \Magento\Quote\Model\Quote)) {
			$this->cachedQuote = $this->_quoteFactory->create()->load($this->getOrder()->getQuoteId());
		}
		return $this->cachedQuote;
	}

	/**
	 * Retrieve store model instance
	 *
	 * @return \Magento\Store\Model\Store
	 */
	public function getStore()
	{
		return $this->_storeManager->getStore($this->getStoreId());
	}

	/**
	 * Retrieve the customer the transaction was created for
	 *
	 * @return \Magento\Customer\Model\Customer
	 */
	public function getCustomer()
	{
		if ($this->getCustomerId() == null) {
			return null;
		}
		if (!($this->cachedCustomer instanceof \Magento\Customer\Model\Customer)) {
			$this->cachedCustomer = $this->_customerFactory->create()->load($this->getCustomerId());
		}
		return $this->cachedCustomer;
	}

	/**
	 * Pull update for this transaction.
	 *
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function update()
	{
		$updateHandler = $this->_container->getBean('Customweb_Payment_Update_IHandler');
		$updateProcessor = new \Customweb_Payment_Update_PullProcessor($updateHandler, $this->getId());
		$updateProcessor->process();
		$this->load($this->getId());
		return $this;
	}

	/**
	 * Get the last error message of a transaction object.
	 *
	 * @return null|string
	 */
	public function getLastErrorMessage()
	{
		if ($this->getTransactionObject() !== null && $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			$errorMessages = $this->getTransactionObject()->getErrorMessages();
			$errorMessage = nl2br((string) end($errorMessages));
			if (!empty($errorMessage)) {
				reset($errorMessages);
				return $errorMessage;
			}
		}

		return __('There has been a problem during the processing of your payment.');
	}

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
		if ($this->getTransactionObject() !== null
				&& $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction) {
			return $this->getTransactionObject()->getTransactionData();
		} else {
			return null;
		}
	}

	/**
	 * Checks if the transaction must be authorized.
	 *
	 * @return boolean
	 */
	protected function isAuthorizationRequired()
	{
		if ($this->getTransactionObject() !== null
				&& $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction
				&& ($this->getTransactionObject()->isAuthorized() || $this->getTransactionObject()->isAuthorizationFailed())
				&& $this->getAuthorizationStatus() === \Customweb_Payment_Authorization_ITransaction::AUTHORIZATION_STATUS_PENDING) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if the a review action is required.
	 *
	 * @return boolean
	 */
	protected function isReviewActionRequired()
	{
		if ($this->getTransactionObject() !== null
				&& $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction
				&& $this->cachedUncertainFlag != null
				&& !$this->getTransactionObject()->isCaptured()
				&& $this->getTransactionObject()->isAuthorizationUncertain() != $this->cachedUncertainFlag) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if the customer is refusing to pay.
	 *
	 * @return boolean
	 */
	protected function isRefusalActionRequired()
	{
		if ($this->getTransactionObject() !== null
				&& $this->getTransactionObject() instanceof \Customweb_Payment_Authorization_ITransaction
				&& $this->cachedRefusingFlag != null
				&& $this->getTransactionObject()->isCustomerRefusingToPay() != $this->cachedRefusingFlag) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Authorize the transaction.
	 */
	protected function authorize()
	{
		try {
			$this->logger->logInfo("Start authorization for transaction " . $this->getId());

			$context = $this->_authorizationMethodFactory->getContextFactory()->createTransaction($this);
			/* @var $adapter \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod */
			$adapter = $this->_authorizationMethodFactory->create($context);
			$adapter->finishAuthorization();

			$this->logger->logInfo("Finish authorization for transaction " . $this->getId());
		} catch(\Exception $e) {
			$this->logger->logException($e);
			throw $e;
		}
	}

	/**
	 * Register review action.
	 */
	protected function registerReviewAction()
	{
		if ($this->getTransactionObject()->isUncertainTransactionFinallyDeclined()) {
			$this->getOrderPayment()->deny();
		} else {
			$this->getOrderPayment()->accept();
		}
		$this->getOrder()->save();
	}

	/**
	 * Handle the situation when the customer is refusing to pay.
	 */
	protected function handleRefusal()
	{
		// TODO: Handle Refusal
	}

	public function getTransactionObject(){
		$transactionObject = $this->getData('transaction_object');
		if(is_string($transactionObject)){
			$this->getResource()->unserializeTransactionObject($this);
			return $this->getData('transaction_object');
		}
		return $transactionObject;

	}
}