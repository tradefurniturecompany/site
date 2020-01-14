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

class OrderContext implements \Customweb_Payment_Authorization_IOrderContext
{
	/**
	 * @var \Magento\Payment\Helper\Data
	 */
	protected $_paymentHelper;

	/**
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * @var \Magento\Directory\Model\RegionFactory
	 */
	protected $_regionFactory;

	/**
	 * @var string
	 */
	private $checkoutId;

	/**
	 * @var int
	 */
	private $customerId;

	/**
	 * @var string
	 */
	private $newCustomer;

	/**
	 * @var int|string
	 */
	private $customerRegistrationDate;

	/**
	 * @var string
	 */
	private $customerEmailAddress;

	/**
	 * @var string
	 */
	private $currencyCode;

	/**
	 * @var \Customweb_Payment_Authorization_IInvoiceItem[]
	 */
	private $invoiceItems;

	/**
	 * @var string
	 */
	private $shippingMethod;

	/**
	 * @var string
	 */
	private $paymentMethod;

	/**
	 * @var string
	 */
	private $language;

	/**
	 * @var array
	 */
	private $orderParameters = [];

	/**
	 * @var \Customweb_Payment_Authorization_OrderContext_IAddress
	 */
	private $shippingAddress = null;

	/**
	 * @var \Customweb_Payment_Authorization_OrderContext_IAddress
	 */
	private $billingAddress = null;

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Directory\Model\RegionFactory $regionFactory
	 * @param \Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param \Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $order
	 * @param \Customweb\RealexCw\Model\Payment\Method\AbstractMethod|string $paymentMethod
	 * @throws \Exception
	 */
	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\CustomerFactory $customerFactory,
			\Magento\Directory\Model\RegionFactory $regionFactory,
			\Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper,
			\Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper,
			$order,
			$paymentMethod
	) {
		$this->_customerFactory = $customerFactory;
		$this->_regionFactory = $regionFactory;

		if ($paymentMethod instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
			$this->paymentMethod = $paymentMethod->getCode();
		} elseif (is_string($paymentMethod)) {
			$this->paymentMethod = $paymentMethod;
		} else {
			throw new \Exception("Unsupported type for argument 'paymentMethod'.");
		}

		if ($order instanceof \Magento\Sales\Model\Order) {
			$this->assembleDataFromOrder($invoiceItemHelper, $foomanSurchargeHelper, $scopeConfig, $checkoutSession, $order);
		} elseif ($order instanceof \Magento\Quote\Model\Quote) {
			$this->assembleDataFromQuote($invoiceItemHelper, $foomanSurchargeHelper, $scopeConfig, $checkoutSession, $order);
		} else {
			throw new \Exception("Unsupported type for argument 'order'.");
		}
	}
	
	public function isAjaxReloadRequired(){
		return false;
	}

	/**
	 * @return \Magento\Payment\Helper\Data
	 */
	private function getPaymentHelper()
	{
		if (!($this->_paymentHelper instanceof \Magento\Payment\Helper\Data)) {
			$this->_paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Payment\Helper\Data');
		}
		return $this->_paymentHelper;
	}

	/**
	 * @return string[]
	 */
	public function __sleep()
	{
		$properties = array_keys(get_object_vars($this));
		$properties = array_diff($properties, ['_paymentHelper', '_customerFactory', '_regionFactory']);
		return $properties;
	}

	/**
	 * Returns {@code true} if the order context contains the necessary information.
	 *
	 * @return boolean
	 */
	public function isValid() {
		if ($this->getBillingAddress()->getCountryIsoCode() == null
			|| $this->getBillingAddress()->getFirstName() == null
			|| $this->getBillingAddress()->getLastName() == null
			|| $this->getBillingAddress()->getStreet() == null
			|| $this->getBillingAddress()->getCity() == null
			|| $this->getBillingAddress()->getPostCode() == null) {
			return false;
		} else {
			return true;
		}
	}

	public function getCheckoutId()
	{
		return $this->checkoutId;
	}

	public function getCustomerId()
	{
		return $this->customerId;
	}

	public function isNewCustomer()
	{
		return $this->newCustomer;
	}

	public function getCustomerRegistrationDate()
	{
		return new \DateTime($this->customerRegistrationDate);
	}

	public function getCustomerEMailAddress()
	{
		return $this->customerEmailAddress;
	}

	public function getOrderAmountInDecimals()
	{
		return \Customweb_Util_Invoice::getTotalAmountIncludingTax($this->getInvoiceItems());
	}

	public function getCurrencyCode()
	{
		return $this->currencyCode;
	}

	public function getInvoiceItems()
	{
		return $this->invoiceItems;
	}

	public function getShippingMethod()
	{
		return $this->shippingMethod;
	}

	public function getPaymentMethod()
	{
		return $this->getPaymentHelper()->getMethodInstance($this->paymentMethod);
	}

	public function getLanguage()
	{
		return new \Customweb_Core_Language($this->language);
	}

	public function getOrderParameters()
	{
		return $this->orderParameters;
	}

	public function getShippingAddress(){
		return $this->shippingAddress;
	}

	public function getBillingAddress(){
		if ($this->billingAddress->getCountryIsoCode() == null) {
			return $this->shippingAddress;
		} else {
			return $this->billingAddress;
		}
	}

	/**
	 * @deprecated
	 */
	public function getBillingEMailAddress() {
		return $this->getBillingAddress()->getEMailAddress();
	}

	/**
	 * @deprecated
	 */
	public function getBillingGender() {
		return $this->getBillingAddress()->getGender();
	}

	/**
	 * @deprecated
	 */
	public function getBillingSalutation() {
		return $this->getBillingAddress()->getSalutation();
	}

	/**
	 * @deprecated
	 */
	public function getBillingFirstName() {
		return $this->getBillingAddress()->getFirstName();
	}

	/**
	 * @deprecated
	 */
	public function getBillingLastName() {
		return $this->getBillingAddress()->getLastName();
	}

	/**
	 * @deprecated
	 */
	public function getBillingStreet() {
		return $this->getBillingAddress()->getStreet();
	}

	/**
	 * @deprecated
	 */
	public function getBillingCity() {
		return $this->getBillingAddress()->getCity();
	}

	/**
	 * @deprecated
	 */
	public function getBillingPostCode() {
		return $this->getBillingAddress()->getPostCode();
	}

	/**
	 * @deprecated
	 */
	public function getBillingState() {
		return $this->getBillingAddress()->getState();
	}

	/**
	 * @deprecated
	 */
	public function getBillingCountryIsoCode() {
		return $this->getBillingAddress()->getCountryIsoCode();
	}

	/**
	 * @deprecated
	 */
	public function getBillingPhoneNumber() {
		return $this->getBillingAddress()->getPhoneNumber();
	}

	/**
	 * @deprecated
	 */
	public function getBillingMobilePhoneNumber() {
		return $this->getBillingAddress()->getMobilePhoneNumber();
	}

	/**
	 * @deprecated
	 */
	public function getBillingDateOfBirth() {
		return $this->getBillingAddress()->getDateOfBirth();
	}

	/**
	 * @deprecated
	 */
	public function getBillingCommercialRegisterNumber() {
		return $this->getBillingAddress()->getCommercialRegisterNumber();
	}

	/**
	 * @deprecated
	 */
	public function getBillingCompanyName() {
		return $this->getBillingAddress()->getCompanyName();
	}

	/**
	 * @deprecated
	 */
	public function getBillingSalesTaxNumber() {
		return $this->getBillingAddress()->getSalesTaxNumber();
	}

	/**
	 * @deprecated
	 */
	public function getBillingSocialSecurityNumber() {
		return $this->getBillingAddress()->getSocialSecurityNumber();
	}

	/**
	 * @deprecated
	 */
	public function getShippingEMailAddress() {
		return $this->getShippingAddress()->getEMailAddress();
	}

	/**
	 * @deprecated
	 */
	public function getShippingGender() {
		return $this->getShippingAddress()->getGender();
	}

	/**
	 * @deprecated
	 */
	public function getShippingSalutation() {
		return $this->getShippingAddress()->getSalutation();
	}

	/**
	 * @deprecated
	 */
	public function getShippingFirstName() {
		return $this->getShippingAddress()->getFirstName();
	}

	/**
	 * @deprecated
	 */
	public function getShippingLastName() {
		return $this->getShippingAddress()->getLastName();
	}

	/**
	 * @deprecated
	 */
	public function getShippingStreet() {
		return $this->getShippingAddress()->getStreet();
	}

	/**
	 * @deprecated
	 */
	public function getShippingCity() {
		return $this->getShippingAddress()->getCity();
	}

	/**
	 * @deprecated
	 */
	public function getShippingPostCode() {
		return $this->getShippingAddress()->getPostCode();
	}

	/**
	 * @deprecated
	 */
	public function getShippingState() {
		return $this->getShippingAddress()->getState();
	}

	/**
	 * @deprecated
	 */
	public function getShippingCountryIsoCode() {
		return $this->getShippingAddress()->getCountryIsoCode();
	}

	/**
	 * @deprecated
	 */
	public function getShippingPhoneNumber() {
		return $this->getShippingAddress()->getPhoneNumber();
	}

	/**
	 * @deprecated
	 */
	public function getShippingMobilePhoneNumber() {
		return $this->getShippingAddress()->getMobilePhoneNumber();
	}

	/**
	 * @deprecated
	 */
	public function getShippingDateOfBirth() {
		return $this->getShippingAddress()->getDateOfBirth();
	}

	/**
	 * @deprecated
	 */
	public function getShippingCompanyName() {
		return $this->getShippingAddress()->getCompanyName();
	}

	/**
	 * @deprecated
	 */
	public function getShippingCommercialRegisterNumber() {
		return $this->getShippingAddress()->getCommercialRegisterNumber();
	}

	/**
	 * @deprecated
	 */
	public function getShippingSalesTaxNumber() {
		return $this->getShippingAddress()->getSalesTaxNumber();
	}

	/**
	 * @deprecated
	 */
	public function getShippingSocialSecurityNumber() {
		return $this->getShippingAddress()->getSocialSecurityNumber();
	}

	/**
	 * @param \Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Quote\Model\Quote $quote
	 */
	private function assembleDataFromQuote(
			\Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper,
			\Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Quote\Model\Quote $quote
	) {
		$useBaseCurrency = $this->getPaymentMethod()->isUseBaseCurrency();
		$checkoutId = $checkoutSession->getRealexCwCheckoutId();
		if(empty($checkoutId)){
			$checkoutId = \Customweb_Core_Util_Rand::getUuid();
			$checkoutSession->setRealexCwCheckoutId($checkoutId);
		}

		$this->checkoutId = $checkoutId;
		$this->customerId = $quote->getCustomerId();
		$this->newCustomer = 'new';
		$this->customerRegistrationDate = $quote->getCustomer()->getCreatedAt();
		$this->customerEmailAddress = $quote->getCustomerEmail();
		$this->assembleCurrency($quote->getStore(), $useBaseCurrency);
		$address = ($quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress());
		$this->invoiceItems = $invoiceItemHelper->getInvoiceItems(
				$quote->getAllItems(),
				$quote->getBillingAddress(),
				$quote->getShippingAddress(),
				$quote->getStore(),
				$useBaseCurrency ? $address->getBaseDiscountAmount() : $address->getDiscountAmount(),
				$useBaseCurrency ? $address->getBaseDiscountTaxCompensationAmount() : $address->getDiscountTaxCompensationAmount(),
				$address->getDiscountDescription(),
				$useBaseCurrency ? $address->getBaseShippingInclTax() : $address->getShippingInclTax(),
				$useBaseCurrency ? $address->getBaseShippingTaxAmount() : $address->getShippingTaxAmount(),
				$address->getShippingDescription(),
				$quote->getCustomerId(),
				$useBaseCurrency ? $quote->getBaseGrandTotal() : $quote->getGrandTotal(),
				$useBaseCurrency,
				$foomanSurchargeHelper->getQuoteSurchargeAmount($quote->getId())
		);
		$this->shippingMethod = ($quote->getShippingAddress() != null) ? $quote->getShippingAddress()->getShippingMethod() : null;
		$this->language = $this->assembleLanguage($scopeConfig, $quote->getStore());
		$this->assembleAddresses($quote->getBillingAddress(), $quote->getShippingAddress(), $quote->getCustomerGender(), $quote->getCustomerDob(), $quote->getCustomerTaxvat());
	}

	/**
	 * @param \Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Sales\Model\Order $order
	 */
	private function assembleDataFromOrder(
			\Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper,
			\Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Sales\Model\Order $order
	) {
		$useBaseCurrency = $this->getPaymentMethod()->isUseBaseCurrency();
		$checkoutId = $checkoutSession->getRealexCwCheckoutId();
		if(empty($checkoutId)){
			$checkoutId = \Customweb_Core_Util_Rand::getUuid();
			$checkoutSession->setRealexCwCheckoutId($checkoutId);
		}

		$this->checkoutId = $checkoutId;
		$this->customerId = $order->getCustomerId();
		$this->newCustomer = 'new';
		$this->customerRegistrationDate = $this->_customerFactory->create()->load($order->getCustomerId())->getCreatedAt();
		$this->customerEmailAddress = $order->getCustomerEmail();
		$this->assembleCurrency($order->getStore(), $useBaseCurrency);
		$this->invoiceItems = $invoiceItemHelper->getInvoiceItems(
				$order->getAllItems(),
				$order->getBillingAddress(),
				$order->getShippingAddress(),
				$order->getStore(),
				$useBaseCurrency ? $order->getBaseDiscountAmount() : $order->getDiscountAmount(),
				$useBaseCurrency ? $order->getBaseDiscountTaxCompensationAmount() : $order->getDiscountTaxCompensationAmount(),
				$order->getDiscountDescription(),
				$useBaseCurrency ? $order->getBaseShippingInclTax() : $order->getShippingInclTax(),
				$useBaseCurrency ? $order->getBaseShippingTaxAmount() : $order->getShippingTaxAmount(),
				$order->getShippingDescription(),
				$order->getCustomerId(),
				$useBaseCurrency ? $order->getBaseGrandTotal() : $order->getGrandTotal(),
				$useBaseCurrency,
				$foomanSurchargeHelper->getQuoteSurchargeAmount($order->getQuoteId())
		);
		$this->shippingMethod = $order->getShippingMethod();
		$this->language = $this->assembleLanguage($scopeConfig, $order->getStore());
		$this->assembleAddresses($order->getBillingAddress(), $order->getShippingAddress(), $order->getCustomerGender(), $order->getCustomerDob(), $order->getCustomerTaxvat());
	}

	/**
	 * @param \Magento\Store\Model\Store $store
	 * @param boolean $useBaseCurrency
	 */
	private function assembleCurrency(\Magento\Store\Model\Store $store, $useBaseCurrency)
	{
		$this->currencyCode = $store->getCurrentCurrencyCode();
		if ($useBaseCurrency) {
			$this->currencyCode = $store->getBaseCurrencyCode();
		}
	}

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Store\Model\Store $store
	 */
	private function assembleLanguage(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\Store $store)
	{
		return $scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
	}

	/**
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $billingAddress
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $shippingAddress
	 * @param null|string $gender
	 * @param null|string $dateOfBirth
	 * @param null|string $taxVat
	 */
	private function assembleAddresses($billingAddress, $shippingAddress, $gender, $dateOfBirth, $taxVat)
	{
		$this->billingAddress = $this->getAddress($billingAddress, $gender, $dateOfBirth, $taxVat);
		$this->shippingAddress = $this->getAddress($shippingAddress != null ? $shippingAddress : $billingAddress, $gender, $dateOfBirth, $taxVat);
	}

	/**
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $address
	 * @param null|string $gender
	 * @param null|string $dateOfBirth
	 * @param null|string $taxVat
	 * @return \Customweb_Payment_Authorization_OrderContext_IAddress
	 */
	private function getAddress($address, $gender, $dateOfBirth, $taxVat)
	{
		$contextAddress = new \Customweb_Payment_Authorization_OrderContext_Address_Default();
		$contextAddress->setEMailAddress($address->getEmail());
		$contextAddress->setGender($gender == 1 ? 'male' : $gender == 2 ? 'female' : '');
		$contextAddress->setSalutation($address->getPrefix());
		$contextAddress->setFirstName($address->getFirstname());
		$contextAddress->setLastName($address->getLastname());
		$contextAddress->setStreet(implode(' ', $address->getStreet()));
		$contextAddress->setCity($address->getCity());
		$contextAddress->setPostCode($address->getPostcode());
		$contextAddress->setState($this->_regionFactory->create()->load(((!$address->getRegionId() && is_numeric($address->getRegion())) ? $address->getRegion() : $address->getRegionId()))->getCode());
		$contextAddress->setCountryIsoCode($address->getCountryId());
		$contextAddress->setPhoneNumber($address->getTelephone());
		$contextAddress->setDateOfBirth(!empty($dateOfBirth) ? new \DateTime($dateOfBirth) : null);
		$contextAddress->setCompanyName($address->getCompany());
		$contextAddress->setSalesTaxNumber(!empty($taxVat) ? $taxVat : null);
		return $contextAddress;
	}
}