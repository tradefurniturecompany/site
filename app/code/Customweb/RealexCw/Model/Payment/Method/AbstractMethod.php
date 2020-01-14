<?php
/**
 *  * You are allowed to use this API in your web application.
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
 * @category Customweb
 * @package Customweb_RealexCw
 *
 */
namespace Customweb\RealexCw\Model\Payment\Method;

class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod implements \Customweb_Payment_Authorization_IPaymentMethod
{
	/**
	 *
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 *
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 *
	 * @var \Magento\Framework\DB\TransactionFactory
	 */
	protected $_dbTransactionFactory;

	/**
	 * @var \Magento\Framework\Encryption\EncryptorInterface
	 */
	protected $_encryptor;

	/**
	 * @var \Magento\Framework\Pricing\PriceCurrencyInterface
	 */
	protected $_priceCurrency;

	/**
	 *
	 * @var \Customweb\RealexCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;

	/**
	 *
	 * @var \Customweb\RealexCw\Model\Configuration
	 */
	protected $_configuration;

	/**
	 *
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 *
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 *
	 * @var \Customweb\RealexCw\Helper\InvoiceItem
	 */
	protected $_invoiceItemHelper;

	/**
	 * @var \Customweb\RealexCw\Helper\FoomanSurcharge
	 */
	protected $_foomanSurchargeHelper;

	/**
	 * Payment method code
	 *
	 * @var string
	 */
	protected $_code;

	/**
	 * Payment method name
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Form block paths
	 *
	 * @var string
	 */
	protected $_formBlockType = 'Customweb\RealexCw\Block\Payment\Method\Form';

	/**
	 * Info block path
	 *
	 * @var string
	 */
	protected $_infoBlockType = 'Customweb\RealexCw\Block\Payment\Method\Info';

	/**
	 *
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
	 * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
	 * @param \Magento\Payment\Helper\Data $paymentData
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Payment\Model\Method\Logger $logger
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Framework\DB\TransactionFactory $dbTransactionFactory
	 * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
	 * @param \Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Customweb\RealexCw\Model\Configuration $configuration
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param \Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
			\Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
			\Magento\Payment\Helper\Data $paymentData,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Payment\Model\Method\Logger $logger,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Framework\App\RequestInterface $request,
			\Magento\Framework\DB\TransactionFactory $dbTransactionFactory,
			\Magento\Framework\Encryption\EncryptorInterface $encryptor,
			\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
			\Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			\Customweb\RealexCw\Model\Configuration $configuration,
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper,
			\Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
	) {
		parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource,
				$resourceCollection, $data);
		$this->_checkoutSession = $checkoutSession;
		$this->_request = $request;
		$this->_dbTransactionFactory = $dbTransactionFactory;
		$this->_encryptor = $encryptor;
		$this->_priceCurrency = $priceCurrency;
		$this->_authorizationMethodFactory = $authorizationMethodFactory;
		$this->_configuration = $configuration;
		$this->_container = $container;
		$this->_transactionFactory = $transactionFactory;
		$this->_invoiceItemHelper = $invoiceItemHelper;
		$this->_foomanSurchargeHelper = $foomanSurchargeHelper;
	}

	public function setStore($storeId)
	{
		parent::setStore($storeId);
		$this->_configuration->setStore($storeId);
	}

	public function getPaymentMethodName()
	{
		return $this->_name;
	}

	public function getPaymentMethodDisplayName()
	{
		return $this->getPaymentMethodConfigurationValue('title');
	}

	public function getPaymentMethodConfigurationValue($key, $languageCode = null)
	{
		$rawValue = $this->_configuration->getConfigurationValue('payment', $this->_code . '/' . $key);
		if (\in_array($this->_code . '/' . $key, [
			
		])) {
			return $this->_encryptor->decrypt($rawValue);
		} else {
			return $rawValue;
		}
	}

	public function existsPaymentMethodConfigurationValue($key, $languageCode = null)
	{
		return $this->_configuration->existsConfiguration('payment', $this->_code . '/' . $key);
	}

	/**
	 * Get description text
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return trim($this->getPaymentMethodConfigurationValue('description'));
	}

	/**
	 * Should show image
	 *
	 * @return boolean
	 */
	public function isShowImage()
	{
		return (boolean) $this->getPaymentMethodConfigurationValue('show_image');
	}

	/**
	 * Should use base currency
	 *
	 * @return boolean
	 */
	public function isUseBaseCurrency()
	{
		return (boolean) $this->getPaymentMethodConfigurationValue('base_currency');
	}

	/**
	 *
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl()
	{
		$quote = $this->_checkoutSession->getQuote();
		$quote->setIsActive(true);
		$quote->setReservedOrderId(null);
		$quote->save();

		$transactionId = null;
		$transaction = $this->_registry->registry('realexcw_transaction');
		if ($transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction) {
			$transactionId = $transaction->getId();
		}
		return $quote->getStore()->getUrl('realexcw/checkout/error', [
			'_secure' => true,
			'transaction_id' => $transactionId
		]);
	}

	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
	{
		$isAvailable = parent::isAvailable($quote);

		if ($isAvailable) {
			$allowedCurrencies = $this->getPaymentMethodConfigurationValue('currency');
			if ($quote !== null && !empty($allowedCurrencies)) {
				$isAvailable = (in_array($quote->getCurrency()->getQuoteCurrencyCode(), $allowedCurrencies));
			}
		}

		if ($isAvailable) {
			try {
				$context = $this->getAuthorizationMethodFactory()->getContextFactory()->createQuote($this, $quote);
				$adapter = $this->getAuthorizationMethodFactory()->create($context);
				$adapter->preValidate();
			}
			catch (\Exception $e) {
				$isAvailable = false;
			}
		}

		return $isAvailable;
	}

	public function validate()
	{
		
		$arguments = null;
		return \Customweb_Licensing_RealexCw_License::run('mp11sm250fe69fp4', $this, $arguments);
	}

	final public function call_46pgge4dq0n4naev() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	private function parentValidate()
	{
		parent::validate();
	}

	/**
	 * Set initial order status to pending payment.
	 *
	 * @param string $paymentAction
	 * @param \Magento\Framework\Object $stateObject
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function initialize($paymentAction, $stateObject)
	{
		$state = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
		$stateObject->setState($state);
		$stateObject->setStatus('pending_payment');
		$stateObject->setIsNotified(false);
		return $this;
	}

	/**
	 * Set transaction id and set transaction as pending if authorization is uncertain.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
	{
		parent::authorize($payment, $amount);

		$transaction = $this->_registry->registry('realexcw_authorization_transaction');
		if ($transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction) {
			$payment->setIsTransactionClosed(false);
			if ($transaction->getTransactionObject()->isAuthorizationUncertain()) {
				$payment->setIsTransactionPending(true);
			}
		}
		return $this;
	}

	/**
	 * Capture amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
	{
		parent::capture($payment, $amount);

		
		try {
			$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
			if ($transaction->getId()) {
				$invoice = $this->_registry->registry('realexcw_invoice');
				$isNoClose = $this->isCaptureNoClose();
				$items = [];
				if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
					$items = $this->_invoiceItemHelper->getInvoiceItems(
						$invoice->getAllItems(),
						$invoice->getBillingAddress(),
						$invoice->getShippingAddress(),
						$invoice->getStore(),
						$this->isUseBaseCurrency() ? $invoice->getBaseDiscountAmount() : $invoice->getDiscountAmount(),
						$this->isUseBaseCurrency() ? $invoice->getBaseDiscountTaxCompensationAmount() : $invoice->getDiscountTaxCompensationAmount(),
						$invoice->getDiscountDescription(),
						$this->isUseBaseCurrency() ? $invoice->getBaseShippingInclTax() : $invoice->getShippingInclTax(),
						$this->isUseBaseCurrency() ? $invoice->getBaseShippingTaxAmount() : $invoice->getShippingTaxAmount(),
						$invoice->getOrder()->getShippingDescription(), $invoice->getOrder()->getCustomerId(),
						$this->isUseBaseCurrency() ? $invoice->getBaseGrandTotal() : $invoice->getGrandTotal(),
						$this->isUseBaseCurrency(),
						$this->_foomanSurchargeHelper->getOrderSurchargeAmount($invoice->getOrder()),
						false,
						$transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getInvoiceItems()
					);
				}
				if (count($items) <= 0) {
					$items = $transaction->getTransactionObject()->getUncapturedLineItems();
				}

				$items = \Customweb_Util_Invoice::getItemsByReductionAmount($items, $this->convertCaptureAmount($amount, $payment->getOrder(), $invoice), $transaction->getCurrency());
				$this->captureItems($transaction, $items);
				$payment->setShouldCloseParentTransaction(!$isNoClose);
				$payment->setIsTransactionPending(false);
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be loaded.'));
			}
		} catch (\Exception $e) {
			if ($e instanceof \Magento\Framework\Exception\LocalizedException) {
				throw $e;
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
			}
		}
		


		return $this;
	}

	private function convertCaptureAmount($amount, \Magento\Sales\Model\Order $order, $invoice) {
		if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
			$amount = $this->_priceCurrency->round($amount * $invoice->getBaseToOrderRate());
			return \min($amount, $invoice->getGrandTotal());
		} else {
			$amount = $this->_priceCurrency->round($amount * $order->getBaseToOrderRate());
			return \min($amount, $order->getGrandTotal());
		}
	}

	/**
	 * Refund amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
	{
		parent::refund($payment, $amount);

		
		try {
			$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
			if ($transaction->getId()) {
				if ($transaction->getTransactionObject()->isRefundPossible()) {
					try {
						$amount = $this->convertRefundAmount($amount, $payment->getOrder(), $payment->getCreditmemo());

						$refundAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_IRefund');
						$compareAmount = \Customweb_Util_Currency::compareAmount($amount, $transaction->getAuthorizationAmount(),
								$transaction->getCurrency());
						if ($compareAmount !== 0) {
							if ($transaction->getTransactionObject()->isPartialRefundPossible()) {
								$creditmemo = $payment->getCreditmemo();
								$items = [];
								if ($creditmemo instanceof \Magento\Sales\Model\Order\Creditmemo) {
									$items = $this->_invoiceItemHelper->getInvoiceItems(
										$creditmemo->getAllItems(),
										$creditmemo->getBillingAddress(),
										$creditmemo->getShippingAddress(),
										$creditmemo->getStore(),
										$this->isUseBaseCurrency() ? $creditmemo->getBaseDiscountAmount() : $creditmemo->getDiscountAmount(),
										$this->isUseBaseCurrency() ? $creditmemo->getBaseDiscountTaxCompensationAmount() : $creditmemo->getDiscountTaxCompensationAmount(),
										$creditmemo->getDiscountDescription(),
										$this->isUseBaseCurrency() ? $creditmemo->getBaseShippingInclTax() : $creditmemo->getShippingInclTax(),
										$this->isUseBaseCurrency() ? $creditmemo->getBaseShippingTaxAmount() : $creditmemo->getShippingTaxAmount(),
										$creditmemo->getOrder()->getShippingDescription(),
										$creditmemo->getOrder()->getCustomerId(),
										$this->isUseBaseCurrency() ? $creditmemo->getBaseGrandTotal() : $creditmemo->getGrandTotal(),
										$this->isUseBaseCurrency(),
										$this->_foomanSurchargeHelper->getOrderSurchargeAmount($creditmemo->getOrder()),
										false,
										$transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getInvoiceItems()
									);
								}
								if (count($items) <= 0) {
									$items = $transaction->getTransactionObject()->getNonRefundedLineItems();
								}
								$items = \Customweb_Util_Invoice::getItemsByReductionAmount($items, $amount, $transaction->getCurrency());
								$refundAdapter->partialRefund($transaction->getTransactionObject(), $items, false);
							}
							else {
								throw new \Magento\Framework\Exception\LocalizedException(__('Partial refund not possible. You may retry with the total transaction amount.'));
							}
						}
						else {
							$refundAdapter->refund($transaction->getTransactionObject());
						}
						$transaction->save();
					}
					catch (\Exception $e) {
						$transaction->save();
						throw $e;
					}
				}
				else {
					throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be refunded online.'));
				}
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be loaded.'));
			}
		} catch (\Exception $e) {
			if ($e instanceof \Magento\Framework\Exception\LocalizedException) {
				throw $e;
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
			}
		}
		


		return $this;
	}

	private function convertRefundAmount($amount, \Magento\Sales\Model\Order $order, $creditmemo) {
		if ($creditmemo instanceof \Magento\Sales\Model\Order\Creditmemo) {
			$amount = $this->_priceCurrency->round($amount * $creditmemo->getBaseToOrderRate());
			return \min($amount, $creditmemo->getGrandTotal());
		} else {
			$amount = $this->_priceCurrency->round($amount * $order->getBaseToOrderRate());
			return \min($amount, $order->getGrandTotal());
		}
	}

	/**
	 * Void amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function void(\Magento\Payment\Model\InfoInterface $payment)
	{
		parent::void($payment);

		
		try {
			$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
			if ($transaction->getId()) {
				if ($transaction->getTransactionObject()->isCancelPossible()) {
					try {
						$cancelAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICancel');
						$cancelAdapter->cancel($transaction->getTransactionObject());
						$transaction->save();
					}
					catch (\Exception $e) {
						$transaction->save();
						throw $e;
					}
				}
				else {
					throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be cancelled online.'));
				}
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be loaded.'));
			}
		} catch (\Exception $e) {
			if ($e instanceof \Magento\Framework\Exception\LocalizedException) {
				throw $e;
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
			}
		}
		


		return $this;
	}

	public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
	{
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isCapturePossible()) {
				$this->captureItems($transaction, $transaction->getTransactionObject()->getUncapturedLineItems());
			}
		} else {
			throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be loaded.'));
		}
		return true;
	}

	public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
	{
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isCancelPossible()) {
				$this->void($payment);
			}
			elseif ($transaction->getTransactionObject()->isCaptured()) {
				// TODO: If transaction is captured, we need to issue a refund.
			}
		} else {
			throw new \Magento\Framework\Exception\LocalizedException(__('The transaction cannot be loaded.'));
		}
		return true;
	}

	public function assignData(\Magento\Framework\DataObject $data)
	{
		parent::assignData($data);
		$infoInstance = $this->getInfoInstance();
		//Since 2.1 the alias and form values are stored in the additional_data array
		if ($data->getData('additional_data') !== null) {
			$infoInstance->setAdditionalInformation('alias', $data->getData('additional_data/alias'));
			foreach ($data->getData('additional_data') as $key => $value) {
				if (strpos($key, 'form[') === 0) {
					$infoInstance->setAdditionalInformation(substr($key, 5, -1), $value);
				}
			}
		}
		else {
			$infoInstance->setAdditionalInformation('alias', $data->getData('alias'));

			foreach ($data->getData() as $key => $value) {
				if (strpos($key, 'form[') === 0) {
					$infoInstance->setAdditionalInformation(substr($key, 5, -1), $value);
				}
			}
		}
		return $this;
	}

	private function captureItems(\Customweb\RealexCw\Model\Authorization\Transaction $transaction, $items = [])
	{
		
		if ($transaction->getTransactionObject()->isCapturePossible()) {
			try {
				$captureAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICapture');
				if ($transaction->getTransactionObject()->isPartialCapturePossible()) {
					$isNoClose = $this->isCaptureNoClose();
					$captureAdapter->partialCapture($transaction->getTransactionObject(), $items, !$isNoClose);
				}
				else {
					$isNoClose = false;
					$captureAdapter->capture($transaction->getTransactionObject());
				}
				$transaction->save();
			}
			catch (\Exception $e) {
				$transaction->save();
				throw $e;
			}
		}
		elseif ($transaction->getTransactionObject()->isCaptured()) {
			return;
		}
		else {
			throw new \Exception(__('The transaction cannot be captured online.'));
		}
		
	}

	/**
	 *
	 * @return boolean
	 */
	private function isCaptureNoClose()
	{
		if ($this->_request->getParam('capture_no_close')) {
			return true;
		}
		$invoice = $this->_request->getParam('invoice');
		if (is_array($invoice) && isset($invoice['capture_no_close']) && $invoice['capture_no_close']) {
			return true;
		}
		return false;
	}

	private function getAuthorizationMethodFactory()
	{
		return $this->_authorizationMethodFactory;
	}

	private function getRegistry()
	{
		return $this->_registry;
	}
}
