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

namespace Customweb\RealexCw\Helper;

class ExternalCheckout extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magento\Customer\Api\AccountManagementInterface
	 */
	protected $_customerAccountManagement;

	/**
	 * @var \Magento\Customer\Model\Metadata\FormFactory
	 */
	protected $_customerFormFactory;

	/**
	 * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
	 */
	protected $_extensibleDataObjectConverter;

	/**
	 * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
	 */
	protected $_customerDataFactory;

	/**
	 * @var /Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $_customerRepository;

	/**
	 * @var \Magento\Framework\Api\DataObjectHelper
	 */
	protected $_dataObjectHelper;

	/**
	 * @var \Magento\Framework\DataObject\Copy
	 */
	protected $_objectCopyService;

	/**
	 * @var \Magento\Quote\Model\QuoteManagement
	 */
	protected $_quoteManagement;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
	 * @param \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory
	 * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
	 * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
	 * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
	 * @param \Magento\Framework\DataObject\Copy $objectCopyService
	 * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Customer\Model\Session $customerSession
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
			\Magento\Customer\Model\Metadata\FormFactory $customerFormFactory,
			\Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
			\Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
			\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
			\Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
			\Magento\Framework\DataObject\Copy $objectCopyService,
			\Magento\Quote\Model\QuoteManagement $quoteManagement,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\Session $customerSession
	) {
		parent::__construct($context);
		$this->_customerAccountManagement = $customerAccountManagement;
		$this->_customerFormFactory = $customerFormFactory;
		$this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
		$this->_customerDataFactory = $customerDataFactory;
		$this->_customerRepository = $customerRepository;
		$this->_dataObjectHelper = $dataObjectHelper;
		$this->_objectCopyService = $objectCopyService;
		$this->_quoteManagement = $quoteManagement;
		$this->_checkoutSession = $checkoutSession;
		$this->_customerSession = $customerSession;
	}

	/**
	 * Validate customer data and set some its data for further usage in quote
	 *
	 * Will return either true or array with error messages
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param array $data
	 * @param string $registerMethod
	 * @return bool|array
	 */
	public function validateCustomerData(\Magento\Quote\Model\Quote $quote, array $data, $registerMethod)
	{
		$isCustomerNew = !$quote->getCustomerId();
		$customer = $quote->getCustomer();
		$customerData = $this->_extensibleDataObjectConverter->toFlatArray($customer, [], '\Magento\Customer\Api\Data\CustomerInterface');

		$customerForm = $this->_customerFormFactory->create(
				\Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
				'customer_account_create',
				$customerData,
				$this->_request->isAjax(),
				\Magento\Customer\Model\Metadata\Form::IGNORE_INVISIBLE,
				[]
		);

		if ($isCustomerNew) {
			$customerRequest = $customerForm->prepareRequest($data);
			$customerData = $customerForm->extractData($customerRequest);
		}

		$customerErrors = $customerForm->validateData($customerData);
		if ($customerErrors !== true) {
			return implode(', ', $customerErrors);
		}

		if (!$isCustomerNew) {
			return true;
		}

		$customer = $this->_customerDataFactory->create();
		$this->_dataObjectHelper->populateWithArray(
				$customer,
				$customerData,
				'\Magento\Customer\Api\Data\CustomerInterface'
		);

		if ($registerMethod == \Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_REGISTER) {
			$password = $customerRequest->getParam('customer_password');
			if ($password != $customerRequest->getParam('confirm_password')) {
				return __('Password and password confirmation are not equal.');
			}
			$quote->setPasswordHash($this->_customerAccountManagement->getPasswordHash($password));
		} else {
			$customer->setGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
		}

		$result = $this->_customerAccountManagement->validate($customer);
		if (!$result->isValid()) {
			return implode(', ', $result->getMessages());
		}

		$quote->getBillingAddress()->setEmail($customer->getEmail());

		$this->_objectCopyService->copyFieldsetToTarget(
				'customer_account',
				'to_quote',
				$this->_extensibleDataObjectConverter->toFlatArray($customer, [], '\Magento\Customer\Api\Data\CustomerInterface'),
				$quote
		);

		return true;
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 * @return \Magento\Sales\Model\Order
	 */
	public function saveOrder(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$context->getQuote()->collectTotals()->save();

		$isNewCustomer = false;
		switch ($context->getRegisterMethod()) {
			case \Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_GUEST:
				$this->prepareGuestQuote($context);
				break;
			case \Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_REGISTER:
				$this->prepareNewCustomerQuote($context);
				$isNewCustomer = true;
				break;
			default:
				$this->prepareCustomerQuote($context);
				break;
		}

		$context->getQuote()->getBillingAddress()->setShouldIgnoreValidation(true);
		$context->getQuote()->getShippingAddress()->setShouldIgnoreValidation(true);

		$order = $this->_quoteManagement->submit($context->getQuote());
		if ($isNewCustomer) {
			try {
				$this->involveNewCustomer($context);
			} catch (\Exception $e) {
				$this->_logger->critical($e);
			}
		}

		$this->_checkoutSession->clearHelperData();
		$this->_checkoutSession
			->setLastQuoteId($context->getQuote()->getId())
			->setLastSuccessQuoteId($context->getQuote()->getId())
			->setLastOrderId($order->getId())
			->setLastRealOrderId($order->getIncrementId());

		return $order;
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 */
	private function prepareGuestQuote(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$context->getQuote()->setCustomerId(null)
			->setCustomerEmail($context->getCustomerEmailAddress())
			->setCustomerIsGuest(true)
			->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 */
	private function prepareNewCustomerQuote(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$billing = $context->getQuote()->getBillingAddress();
		$shipping = $context->getQuote()->isVirtual() ? null : $context->getQuote()->getShippingAddress();

		$customer = $context->getQuote()->getCustomer();
		$customerBillingData = $billing->exportCustomerAddress();
		$dataArray = $this->_objectCopyService->getDataFromFieldset('checkout_onepage_quote', 'to_customer', $context->getQuote());
		$this->_dataObjectHelper->populateWithArray(
				$customer,
				$dataArray,
				'\Magento\Customer\Api\Data\CustomerInterface'
		);
		$context->getQuote()->setCustomer($customer)->setCustomerId(true);

		$customerBillingData->setIsDefaultBilling(true);

		if ($shipping) {
			if (!$shipping->getSameAsBilling()) {
				$customerShippingData = $shipping->exportCustomerAddress();
				$customerShippingData->setIsDefaultShipping(true);
				$shipping->setCustomerAddressData($customerShippingData);
				$context->getQuote()->addCustomerAddress($customerShippingData);
			} else {
				$shipping->setCustomerAddressData($customerBillingData);
				$customerBillingData->setIsDefaultShipping(true);
			}
		} else {
			$customerBillingData->setIsDefaultShipping(true);
		}
		$billing->setCustomerAddressData($customerBillingData);
		$context->getQuote()->addCustomerAddress($customerBillingData);
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 */
	private function prepareCustomerQuote(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$billing = $context->getQuote()->getBillingAddress();
		$shipping = $context->getQuote()->isVirtual() ? null : $context->getQuote()->getShippingAddress();

		$customer = $this->_customerRepository->getById($context->getCustomerId());
		$hasDefaultBilling = (bool)$customer->getDefaultBilling();
		$hasDefaultShipping = (bool)$customer->getDefaultShipping();

		if ($shipping && !$shipping->getSameAsBilling() &&
				(!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
		) {
			$shippingAddress = $shipping->exportCustomerAddress();
			if (!$hasDefaultShipping) {
				$shippingAddress->setIsDefaultShipping(true);
				$hasDefaultShipping = true;
			}
			$context->getQuote()->addCustomerAddress($shippingAddress);
			$shipping->setCustomerAddressData($shippingAddress);
		}

		if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
			$billingAddress = $billing->exportCustomerAddress();
			if (!$hasDefaultBilling) {
				if (!$hasDefaultShipping) {
					$billingAddress->setIsDefaultShipping(true);
				}
				$billingAddress->setIsDefaultBilling(true);
			}
			$context->getQuote()->addCustomerAddress($billingAddress);
			$billing->setCustomerAddressData($billingAddress);
		}
	}

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Context $context
	 */
	private function involveNewCustomer(\Customweb\RealexCw\Model\ExternalCheckout\Context $context)
	{
		$customer = $context->getQuote()->getCustomer();
		$this->_customerSession->loginById($customer->getId());
	}
}