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

namespace Customweb\RealexCw\Model\ExternalCheckout;

/**
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setContextId(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setState(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setFailedErrorMessage(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setCartUrl(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setDefaultCheckoutUrl(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setInvoiceItems(\Customweb_Payment_Authorization_IInvoiceItem[] $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setOrderAmountInDecimals(double $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setCurrencyCode(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setLanguageCode(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setCustomerEmailAddress(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setCustomerId(int $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setTransactionId(int $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setShippingAddress(\Customweb_Payment_Authorization_OrderContext_IAddress $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setBillingAddress(\Customweb_Payment_Authorization_OrderContext_IAddress $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setShippingMethodName(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setPaymentMethod(string $value)
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setProviderData(array $value)
 * @method string getCreatedOn()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setCreatedOn(string $value)
 * @method string getUpdatedOn()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setUpdatedOn(string $value)
 * @method string getSecurityToken()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setSecurityToken(string $value)
 * @method string getSecurityTokenExpiryDate()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setSecurityTokenExpiryDate(string $value)
 * @method string getAuthenticationSuccessUrl()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setAuthenticationSuccessUrl(string $value)
 * @method string getAuthenticationEmailAddress()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setAuthenticationEmailAddress(string $value)
 * @method int getQuoteId()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setQuoteId(int $value)
 * @method string getRegisterMethod()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setRegisterMethod(string $value)
 * @method int getStoreId()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setStoreId(int $value)
 * @method int getVersionNumber()
 * @method \Customweb\RealexCw\Model\ExternalCheckout\Context setVersionNumber(int $value)
 */
class Context extends \Magento\Framework\Model\AbstractModel implements \Customweb_Payment_ExternalCheckout_IContext
{
	const REGISTER_METHOD_GUEST = 'guest';
	const REGISTER_METHOD_REGISTER = 'register';

	/**
	 * @var \Magento\Payment\Helper\Data
	 */
	protected $_paymentHelper;

	/**
	 * @var \Magento\Quote\Model\QuoteFactory
	 */
	protected $_quoteFactory;

	/**
	 * @var \Magento\Directory\Model\RegionFactory
	 */
	protected $_regionFactory;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $_customerRepository;

	/**
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @var \Customweb\RealexCw\Helper\InvoiceItem
	 */
	protected $_invoiceItemHelper;

	/**
	 * @var \Customweb\RealexCw\Helper\FoomanSurcharge
	 */
	protected $_foomanSurchargeHelper;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'customweb_realexcw_external_checkout_context';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'external_checkout_context';

	/**
	 * @var \Magento\Quote\Model\Quote
	 */
	private $cachedQuote;

	/**
	 * @var \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	private $cachedPaymentMethod;

	/**
	 * "An undefined class `Magento\Framework\Data\Collection\Db`
	 * in `\Customweb\RealexCw\Model\ExternalCheckout\Context::__construct()`":
	 * https://github.com/tradefurniturecompany/site/issues/133
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Payment\Helper\Data $paymentHelper
	 * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
	 * @param \Magento\Directory\Model\RegionFactory $regionFactory
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param \Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Payment\Helper\Data $paymentHelper,
			\Magento\Quote\Model\QuoteFactory $quoteFactory,
			\Magento\Directory\Model\RegionFactory $regionFactory,
			\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
			\Magento\Customer\Model\CustomerFactory $customerFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Customer\Model\Session $customerSession,
			\Customweb\RealexCw\Helper\InvoiceItem $invoiceItemHelper,
			\Customweb\RealexCw\Helper\FoomanSurcharge $foomanSurchargeHelper,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
	) {
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
		$this->_paymentHelper = $paymentHelper;
		$this->_quoteFactory = $quoteFactory;
		$this->_regionFactory = $regionFactory;
		$this->_customerRepository = $customerRepository;
		$this->_customerFactory = $customerFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_customerSession = $customerSession;
		$this->_invoiceItemHelper = $invoiceItemHelper;
		$this->_foomanSurchargeHelper = $foomanSurchargeHelper;
	}

    protected function _construct() {
        $this->_init('Customweb\RealexCw\Model\ResourceModel\ExternalCheckout\Context');
    }

    /**
     * Load context entry by its quote id
     *
     * @param int $quoteId
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function loadByQuoteId($quoteId)
    {
    	return $this->load($quoteId, 'quote_id');
    }

    /**
     * Load reusable context entry by its quote id
     *
     * @param int $quoteId
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function loadReusableByQuoteId($quoteId)
    {
    	$this->getResource()->loadReusableByQuoteId($this, $quoteId);
    	return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
    	if (!($this->cachedQuote instanceof \Magento\Quote\Model\Quote)) {
    		$this->cachedQuote = $this->_quoteFactory->create()->load($this->getQuoteId());
    	}
    	return $this->cachedQuote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
    	$this->setQuoteId($quote->getId());
    	$this->cachedQuote = $quote;
    	return $this;
    }

    public function getContextId()
    {
    	return $this->getData('context_id');
    }

    public function getState()
    {
    	return $this->getData('state');
    }

    public function getFailedErrorMessage()
    {
    	return $this->getData('failed_error_message');
    }

    public function getCartUrl()
    {
    	return $this->getData('cart_url');
    }

    public function getDefaultCheckoutUrl()
    {
    	return $this->getData('default_checkout_url');
    }

    public function getInvoiceItems()
    {
    	return $this->getData('invoice_items');
    }

    public function getOrderAmountInDecimals()
    {
    	return $this->getData('order_amount_in_decimals');
    }

    public function getCurrencyCode()
    {
    	return $this->getData('currency_code');
    }

    public function getLanguage()
    {
    	return new \Customweb_Core_Language($this->getLanguageCode());
    }

    /**
     * @param string|\Customweb_Core_Language $language
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function setLanguage($language)
    {
		if ($language instanceof \Customweb_Core_Language) {
			$this->setLanguageCode($language->getIso2LetterCode());
		} else {
			$this->setLanguageCode($language);
		}
		return $this;
    }

    public function getCustomerEmailAddress()
    {
    	return $this->getData('customer_email_address');
    }

    public function getCustomerId()
    {
    	return $this->getData('customer_id');
    }

    public function getTransactionId()
    {
    	return $this->getData('transaction_id');
    }

    public function getShippingAddress()
    {
    	return $this->getData('shipping_address');
    }

    public function getBillingAddress()
    {
    	return $this->getData('billing_address');
    }

    public function getShippingMethodName()
    {
    	return $this->getData('shipping_method_name');
    }

    public function getPaymentMethod()
    {
    	if (!($this->cachedPaymentMethod instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod)) {
    		try {
	    		$this->cachedPaymentMethod = $this->_paymentHelper->getMethodInstance($this->getData('payment_method'));
    		} catch (\Exception $e) {
    			return null;
    		}
    	}
    	return $this->cachedPaymentMethod;
    }

    /**
     * @param string|\Customweb\RealexCw\Model\Payment\Method\AbstractMethod $paymentMethod
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function setPaymentMethod($paymentMethod)
    {
    	if ($paymentMethod instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
    		$this->cachedPaymentMethod = $paymentMethod;
    		$this->setData('payment_method', $paymentMethod->getCode());
    	} else {
    		$this->cachedPaymentMethod = null;
    		$this->setData('payment_method', $paymentMethod);
    	}
    	return $this;
    }

    public function getProviderData()
    {
    	return $this->getData('provider_data');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Exception
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function updateFromQuote(\Magento\Quote\Model\Quote $quote)
    {
    	if (!$this->getId()) {
    		throw new \Exception("Before the context can be updated with a quote, it must be stored in the database.");
    	}

		$this->setQuote($quote);

		if ($this->getQuote()->isVirtual()) {
			$this->setShippingMethodName(__('No shipping method needed.'));
		} else {
			$this->setShippingMethodName($quote->getShippingAddress()->getShippingDescription());
		}

		$this->setStoreId($quote->getStore()->getId());
		$this->setLanguageCode($this->assembleLanguage($quote->getStore()));

		$this->setCartUrl($quote->getStore()->getUrl('checkout/cart', ['_secure' => true]));
		$this->setDefaultCheckoutUrl($quote->getStore()->getUrl('checkout/onepage', ['_secure' => true]));

		$address = ($quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress());
		$this->setInvoiceItems($this->_invoiceItemHelper->getInvoiceItems(
				$quote->getAllItems(),
				$quote->getBillingAddress(),
				$quote->getShippingAddress(),
				$quote->getStore(),
				$this->isUseBaseCurrency() ? $address->getBaseDiscountAmount() : $address->getDiscountAmount(),
				$this->isUseBaseCurrency() ? $address->getBaseDiscountTaxCompensationAmount() : $address->getDiscountTaxCompensationAmount(),
				$address->getDiscountDescription(),
				$this->isUseBaseCurrency() ? $address->getBaseShippingInclTax() : $address->getShippingInclTax(),
				$this->isUseBaseCurrency() ? $address->getBaseShippingTaxAmount() : $address->getShippingTaxAmount(),
				$address->getShippingDescription(),
				$quote->getCustomerId(),
				$this->isUseBaseCurrency() ? $quote->getBaseGrandTotal() : $quote->getGrandTotal(),
				$this->isUseBaseCurrency(),
				$this->_foomanSurchargeHelper->getQuoteSurchargeAmount($quote->getId())
		));
		$this->setOrderAmountInDecimals(\Customweb_Util_Invoice::getTotalAmountIncludingTax($this->getInvoiceItems()));
		$this->setCurrencyCode($this->isUseBaseCurrency() ? $this->getQuote()->getStore()->getBaseCurrencyCode() : $this->getQuote()->getStore()->getCurrentCurrencyCode());

		$this->setCustomerEmailAddress($this->getQuote()->getCustomerEmail());
		$this->setCustomerId($this->getQuote()->getCustomerId());

		$this->checkCountry();

		return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function updateQuote(\Magento\Quote\Model\Quote $quote = null)
    {
    	if ($quote instanceof \Magento\Quote\Model\Quote) {
			$this->updateFromQuote($quote);
    	}

		if (!$this->getQuote()->isVirtual()
				&& $this->getShippingAddress() instanceof \Customweb_Payment_Authorization_OrderContext_IAddress) {
			$this->updateQuoteAddress($this->getShippingAddress(), $this->getQuote()->getShippingAddress());
		}

		if ($this->getBillingAddress() instanceof \Customweb_Payment_Authorization_OrderContext_IAddress) {
			$this->updateQuoteAddress($this->getBillingAddress(), $this->getQuote()->getBillingAddress());

			if ($this->getBillingAddress()->getDateOfBirth() instanceof \DateTime) {
				$this->getQuote()->setCustomerDob($this->getBillingAddress()->getDateOfBirth()->format('Y-m-d H:i:s'));
			}

			if ($this->getBillingAddress()->getGender() == 'male') {
				$this->getQuote()->setCustomerGender(1);
			} elseif ($this->getBillingAddress()->getGender() == 'female') {
				$this->getQuote()->setCustomerGender(2);
			} else {
				$this->getQuote()->setCustomerGender(null);
			}
		}

		if ($this->getCustomerId()) {
			$this->getQuote()->setCustomer($this->_customerRepository->getById($this->getCustomerId()));
			$this->getQuote()->setCustomerId($this->getCustomerId());
		} else {
			$this->getQuote()->setCustomerId(null);
		}

		if ($this->getCustomerEmailAddress()) {
			$this->getQuote()->setCustomerEmail($this->getCustomerEmailAddress());
		} else {
			$this->getQuote()->setCustomerEmail(null);
		}

		if ($this->getPaymentMethod() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
			$data = [
				'method' => $this->getPaymentMethod()->getCode(),
				'checks' => [
		            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
		            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
		            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
		            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
		            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
				]
	        ];
	        $payment = $this->getQuote()->getPayment();
	        $payment->importData($data);
		}

		if (!$this->getQuote()->isVirtual() && $this->getQuote()->getShippingAddress()) {
			$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
		}

		if ($this->getQuote()->getShippingAddress()->getShippingMethod() == null) {
			$this->getQuote()->getShippingAddress()->collectShippingRates()->save();
			$shippingRates = $this->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
			if (count($shippingRates) == 1) {
				$rates = current($shippingRates);
				if (count($rates) == 1) {
					$this->getQuote()->getShippingAddress()->setShippingMethod(current($rates)->getCode());
				}
			}
		}

		$this->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();

		$this->updateFromQuote($this->getQuote());

		return $this;
    }

    /**
     * @param string $shippingMethod
     * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
     */
    public function updateShippingMethod($shippingMethod)
    {
    	$this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);
    	$this->updateQuote();
    	return $this;
    }

    public function updateCustomerSession()
    {
		if ($this->_customerSession->isLoggedIn()) {
			return;
		}

		$customer = $this->_customerFactory->create()->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
		if ($this->getCustomerId()) {
			$customer->load($this->getCustomerId());
		} elseif ($this->getCustomerEmailAddress()) {
			$customer->loadByEmail($this->getCustomerEmailAddress());
		} else {
			return;
		}

		if ($customer->getId()) {
			$this->_customerSession->setCustomerAsLoggedIn($customer);
			$this->getQuote()->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER)->save();
		} elseif ($this->getBillingAddress() instanceof \Customweb_Payment_Authorization_OrderContext_IAddress) {
			$this->getQuote()->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
			$this->getQuote()->setCustomerId(null);
			$this->getQuote()->setCustomerEmail($this->getCustomerEmailAddress());
			$this->getQuote()->setCustomerIsGuest(true);
			$this->getQuote()->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
			$this->getQuote()->save();
			$this->setRegisterMethod(self::REGISTER_METHOD_GUEST);
		}

		return $this;
    }

    /**
     * @param \Customweb_Payment_Authorization_OrderContext_IAddress $address
     */
    public function checkAddress(\Customweb_Payment_Authorization_OrderContext_IAddress $address)
    {
    	\Customweb_Core_Assert::hasLength($address->getFirstName(), "The address must contain a firstname.");
    	\Customweb_Core_Assert::hasLength($address->getLastName(), "The address must contain a lastname.");
    	\Customweb_Core_Assert::hasLength($address->getStreet(), "The address must contain a street.");
    	\Customweb_Core_Assert::hasLength($address->getPostCode(), "The address must contain a post code.");
    	\Customweb_Core_Assert::hasLength($address->getCountryIsoCode(), "The address must contain a country.");
    	\Customweb_Core_Assert::hasLength($address->getCity(), "The address must contain a city.");
    }

    public function checkIntegrity()
    {
    	\Customweb_Core_Assert::notNull($this->getBillingAddress(), "The context must contain a billing address, before it can be COMPLETED.");
    	\Customweb_Core_Assert::notNull($this->getShippingAddress(), "The context must contain a shipping address, before it can be COMPLETED. You may use the billing address when no shipping address is present.");
    	\Customweb_Core_Assert::hasLength($this->getShippingMethodName(), "The context must contain a shipping method name, before it can be COMPLETED.");
    	\Customweb_Core_Assert::notNull($this->getBillingAddress(), "The context must contain a billing address, before it can be COMPLETED.");
    	\Customweb_Core_Assert::hasSize($this->getInvoiceItems(), "At least one line item must be added before it can be COMPLETED.");
    	\Customweb_Core_Assert::hasLength($this->getCustomerEmailAddress(), "The context must contain an e-mail address before it can be COMPLETED.");
    }

    /**
     * @param \Customweb_Payment_Authorization_OrderContext_IAddress $source
     * @param \Magento\Quote\Model\Quote\Address $target
     */
    private function updateQuoteAddress(\Customweb_Payment_Authorization_OrderContext_IAddress $source, \Magento\Quote\Model\Quote\Address $target)
    {
    	$target->setEmail($source->getEMailAddress());
    	$target->setFirstname($source->getFirstName());
    	$target->setLastname($source->getLastName());
    	$target->setCompany($source->getCompanyName());
    	$target->setCity($source->getCity());
    	$target->setPostcode($source->getPostCode());
    	$target->setTelephone($source->getPhoneNumber());
    	$target->setStreet($source->getStreet());
    	$target->setCountryId($source->getCountryIsoCode());
    	$target->setRegion($source->getState());

    	$region = $this->_regionFactory->create()->loadByCode($source->getState(), $source->getCountryIsoCode());
    	if ($region->getId()) {
    		$target->setRegion($region->getName())->setRegionId($region->getId());
    	}
    }

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\Store $store
     */
    private function assembleLanguage(\Magento\Store\Model\Store $store)
    {
    	return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
    }

    /**
     * @return boolean
     */
    private function isUseBaseCurrency()
    {
    	if ($this->getPaymentMethod() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
    		return $this->getPaymentMethod()->isUseBaseCurrency();
    	} else {
    		return false;
    	}
    }

    /**
     * @throws \Exception
     */
    private function checkCountry()
    {
    	$allowedCountries = explode(',', (string)$this->_scopeConfig->getValue('general/country/allow'));
    	if ($this->getBillingAddress() instanceof \Customweb_Payment_Authorization_OrderContext_IAddress) {
    		if (!in_array($this->getBillingAddress()->getCountryIsoCode(), $allowedCountries)) {
    			throw new \Exception(__('It is not possible to checkout in your country.'));
    		}
    	}
    	if ($this->getShippingAddress() instanceof \Customweb_Payment_Authorization_OrderContext_IAddress) {
    		if (!in_array($this->getShippingAddress()->getCountryIsoCode(), $allowedCountries)) {
    			throw new \Exception(__('It is not possible to checkout in your country.'));
    		}
    	}
    }
}