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
 * @package		\Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Model\ExternalCheckout;

class Service implements \Customweb_Payment_ExternalCheckout_ICheckoutService
{
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @var \Magento\Checkout\Helper\Data
	 */
	protected $_checkoutHelper;

	/**
	 * @var \Magento\Payment\Helper\Data
	 */
	protected $_paymentHelper;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @var \Magento\Framework\View\LayoutFactory
	 */
	protected $_layoutFactory;

	/**
	 * @var \Magento\Framework\Translate\InlineInterface
	 */
	protected $_translateInline;

	/**
	 * @var \Magento\Checkout\Api\AgreementsValidatorInterface
	 */
	protected $_agreementsValidator;

	/**
	 * @var \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface
	 */
	protected $_checkoutAgreementsRepository;

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @var \Customweb\RealexCw\Model\ExternalCheckout\ContextFactory
	 */
	protected $_contextFactory;

	/**
	 * @var \Customweb\RealexCw\Helper\ExternalCheckout
	 */
	protected $_helper;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionContextFactory
	 */
	protected $_transactionContextFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\OrderContextFactory
	 */
	protected $_orderContextFactory;

	/**
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Checkout\Helper\Data $checkoutHelper
	 * @param \Magento\Payment\Helper\Data $paymentHelper
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\View\LayoutFactory $layoutFactory
	 * @param \Magento\Framework\Translate\InlineInterface $translateInline
	 * @param \Magento\Checkout\Api\AgreementsValidatorInterface $agreementsValidator
	 * @param \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	 * @param \Customweb\RealexCw\Helper\ExternalCheckout $helper
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionContextFactory $transactionContextFactory
	 * @param \Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory
	 */
	public function __construct(
			\Magento\Framework\Message\ManagerInterface $messageManager,
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Helper\Data $checkoutHelper,
			\Magento\Payment\Helper\Data $paymentHelper,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Framework\View\LayoutFactory $layoutFactory,
			\Magento\Framework\Translate\InlineInterface $translateInline,
			\Magento\Checkout\Api\AgreementsValidatorInterface $agreementsValidator,
			\Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository,
			\Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory,
			\Customweb\RealexCw\Helper\ExternalCheckout $helper,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\RealexCw\Model\Authorization\TransactionContextFactory $transactionContextFactory,
			\Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory
	) {
		$this->_messageManager = $messageManager;
		$this->_coreRegistry = $coreRegistry;
		$this->_scopeConfig = $scopeConfig;
		$this->_checkoutHelper = $checkoutHelper;
		$this->_paymentHelper = $paymentHelper;
		$this->_customerSession = $customerSession;
		$this->_layoutFactory = $layoutFactory;
		$this->_translateInline = $translateInline;
		$this->_agreementsValidator = $agreementsValidator;
		$this->_checkoutAgreementsRepository = $checkoutAgreementsRepository;
		$this->_contextFactory = $contextFactory;
		$this->_helper = $helper;
		$this->_transactionFactory = $transactionFactory;
		$this->_transactionContextFactory = $transactionContextFactory;
		$this->_orderContextFactory = $orderContextFactory;
	}

	/**
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @return \Customweb\RealexCw\Model\ExternalCheckout\Service
	 */
	public function setContainer(\Customweb\RealexCw\Model\DependencyContainer $container)
	{
		$this->_container = $container;
		return $this;
	}

	public function loadContext($contextId, $cache = true)
	{
		return $this->_contextFactory->create()->load($contextId);
	}

	public function createSecurityToken(\Customweb_Payment_ExternalCheckout_IContext $context)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}
		$token = \Customweb_Core_Util_Rand::getUuid();

		if($context->getSecurityToken() == null){
			$context->setSecurityToken($token);
			$context->setSecurityTokenExpiryDate(\Customweb_Core_DateTime::_()->addHours(4)->format("Y-m-d H:i:s"));
			$context->save();
		}
		return $context->getSecurityToken();
	}

	public function checkSecurityTokenValidity(\Customweb_Payment_ExternalCheckout_IContext $context, $token)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}
		if ($context->getSecurityToken() === null || $context->getSecurityToken() !== $token) {
			throw new \Customweb_Payment_Exception_ExternalCheckoutInvalidTokenException();
		}
		$expiryDate = $context->getSecurityTokenExpiryDate();
		if (!empty($expiryDate)) {
			$expiryDate = new \Customweb_Core_DateTime($expiryDate);
			if ($expiryDate->getTimestamp() > time()) {
				return;
			}
		}
		throw new \Customweb_Payment_Exception_ExternalCheckoutTokenExpiredException();
	}

	public function markContextAsFailed(\Customweb_Payment_ExternalCheckout_IContext $context, $message)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}
		if ($context->getState() == \Customweb_Payment_ExternalCheckout_IContext::STATE_COMPLETED) {
			throw new \Exception("The external checkout context cannot be set to state FAILED, while the context is already in state COMPLETED.");
		}
		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_FAILED);
		$context->setFailedErrorMessage($message);
		$context->save();

		$this->_messageManager->addError($message);
	}

	public function updateProviderData(\Customweb_Payment_ExternalCheckout_IContext $context, array $data)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}
		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$context->setProviderData($data);
		$context->updateQuote();
		$context->save();
	}

	public function authenticate(\Customweb_Payment_ExternalCheckout_IContext $context, $emailAddress, $successUrl)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		if ($context->getBillingAddress() === null) {
			$billingAddress = new \Customweb_Payment_Authorization_OrderContext_Address_Default();
			$billingAddress->setFirstName('First')->setLastName('Last')->setCity('unknown')->setStreet('unknown 1')->setCountryIsoCode('DE')->setPostCode(
					'10000');
			$context->setBillingAddress($billingAddress);
		}

		if ($this->_customerSession->isLoggedIn()) {
			$context->updateQuote();
			$context->save();
			return \Customweb_Core_Http_Response::redirect($successUrl);
		}

		if ($this->_checkoutHelper->isAllowedGuestCheckout($context->getQuote())
				&& $this->_scopeConfig->getValue('realexcw/shop/external_checkout_account_creation') == 'skip_selection') {
			$this->_helper->validateCustomerData($context->getQuote(), [
				'email' => $emailAddress,
				'firstname' => $context->getBillingAddress()->getFirstName(),
				'lastname' => $context->getBillingAddress()->getLastName(),
			], 'guest');
			$context->getQuote()->collectTotals()->save();
			$context->setRegisterMethod(\Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_GUEST);
			$context->updateQuote($context->getQuote());
			$context->save();
			return \Customweb_Core_Http_Response::redirect($successUrl);
		}

		$context->setAuthenticationEmailAddress($emailAddress);
		$context->setAuthenticationSuccessUrl($successUrl);
		$context->save();
		return \Customweb_Core_Http_Response::redirect($context->getQuote()->getStore()->getUrl('realexcw/externalCheckout/login', ['_secure' => true]));
	}

	public function updateCustomerEmailAddress(\Customweb_Payment_ExternalCheckout_IContext $context, $emailAddress)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$context->setCustomerEmailAddress($emailAddress);
		$context->updateQuote();
		$context->updateCustomerSession();
		$context->save();
	}

	public function updateShippingAddress(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Payment_Authorization_OrderContext_IAddress $address)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->checkAddress($address);
		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$context->setShippingAddress($address);
		$context->updateQuote();
		$context->save();
	}

	public function updateBillingAddress(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Payment_Authorization_OrderContext_IAddress $address)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->checkAddress($address);
		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$context->setBillingAddress($address);
		$context->updateQuote();
		$context->save();
	}

	public function renderShippingMethodSelectionPane(\Customweb_Payment_ExternalCheckout_IContext $context, $errorMessages)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		return $this->_layoutFactory->create()->createBlock('Customweb\RealexCw\Block\ExternalCheckout\ShippingMethods')
			->setContext($context)
			->setErrorMessages($errorMessages)
			->toHtml();
	}

	public function updateShippingMethod(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Core_Http_IRequest $request)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$parameters = $request->getParameters();
		$context->updateShippingMethod($parameters['shipping_method']);
		$context->save();
	}

	public function getPossiblePaymentMethods(\Customweb_Payment_ExternalCheckout_IContext $context)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$paymentMethods = [];
		foreach ($this->_paymentHelper->getStoreMethods($context->getQuote()->getStore()->getId()) as $paymentMethod) {
			if ($paymentMethod instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
				$paymentMethods[] = $paymentMethod;
			}
		}
		return $paymentMethods;
	}

	public function updatePaymentMethod(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Payment_Authorization_IPaymentMethod $method)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}
		$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
		$context->setPaymentMethod($method);
		$context->updateQuote();
		$context->save();
	}

	public function renderReviewPane(\Customweb_Payment_ExternalCheckout_IContext $context, $renderConfirmationFormElements, $errorMessage)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$layout = $this->_layoutFactory->create();
		$layout->getUpdate()->load(['realexcw_externalcheckout_review']);
		$layout->generateXml();
		$layout->generateElements();
		$layout->getBlock('customweb_realexcw_externalcheckout_review')
			->setContext($context)
			->setErrorMessages($errorMessage)
			->setRenderConfirmationFormElements($renderConfirmationFormElements);
		$output = $layout->getOutput();
		$this->_translateInline->processResponseBody($output);
		return $output;
	}

	public function validateReviewForm(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Core_Http_IRequest $request)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->updateQuote()->save();

		if ($context->getShippingMethodName() == null) {
			throw new \Exception(__('Please select a shipping method before sending the order.'));
		}

		if ($this->isAgreementEnabled()) {
			$parameters = $request->getParameters();
			$agreementParameters = [];
			if (isset($parameters['agreement'])) {
				$agreementParameters = $parameters['agreement'];
			}
			if (!$this->_agreementsValidator->isValid(array_keys($agreementParameters))) {
				throw new \Exception(__('Please agree to all the terms and conditions before placing the order.'));
			}
		}
	}

	public function renderAdditionalFormElements(\Customweb_Payment_ExternalCheckout_IContext $context, $errorMessage)
	{
		return;
	}

	public function processAdditionalFormElements(\Customweb_Payment_ExternalCheckout_IContext $context, \Customweb_Core_Http_IRequest $request)
	{

	}

	public function createOrder(\Customweb_Payment_ExternalCheckout_IContext $context)
	{
		if (!($context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			throw new \Customweb_Core_Exception_CastException('Customweb\RealexCw\Model\ExternalCheckout\Context');
		}

		$context->updateQuote()->save();

		if ($context->getState() == \Customweb_Payment_ExternalCheckout_IContext::STATE_COMPLETED) {
			$transcationId = $context->getTransactionId();
			if (empty($transcationId)) {
				throw new \Exception("Invalid state. The context can not be in state COMPLETED without transaction id set.");
			}
			return $this->_transactionFactory->create()->load($transcationId);
		} elseif ($context->getState() == \Customweb_Payment_ExternalCheckout_IContext::STATE_FAILED) {
			throw new \Exception("A failed context cannot be completed.");
		}

		$context->checkIntegrity();

		$this->_transactionFactory->create()->getResource()->beginTransaction();
		try {
			$this->_coreRegistry->register('initializeTransaction', false);

			$order = $this->_helper->saveOrder($context);

			/* @var $transaction \Customweb\RealexCw\Model\Authorization\Transaction */
			$transaction = $this->_coreRegistry->registry('realexcw_transaction');

			$transactionContext = $this->_transactionContextFactory->create([
				'transaction' => $transaction,
				'orderContext' => $this->_orderContextFactory->create(['order' => $order, 'paymentMethod' => $order->getPayment()->getMethodInstance()]),
				'orderId' => $order->getId(),
				'aliasTransactionId' => null
			]);

			/* @var $provider \Customweb_Payment_ExternalCheckout_IProviderService */
			$provider = $this->_container->getBean('Customweb_Payment_ExternalCheckout_IProviderService');
			$transactionObject = $provider->createTransaction($transactionContext, $context);
			$transaction->setTransactionObject($transactionObject);
			$transaction->save();

			$context->setTransactionId($transaction->getId());
			$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_COMPLETED);
			$context->save();

			$this->_transactionFactory->create()->getResource()->commit();
			return $transactionObject;
		} catch (\Exception $e) {
			$this->_transactionFactory->create()->getResource()->rollBack();
			throw $e;
		}
	}

	/**
	 * Verify if agreement validation needed
	 * @return bool
	 */
	private function isAgreementEnabled()
	{
		$isAgreementsEnabled = $this->_scopeConfig->isSetFlag(
				\Magento\CheckoutAgreements\Model\AgreementsProvider::PATH_ENABLED,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
		$agreementsList = $isAgreementsEnabled ? $this->_checkoutAgreementsRepository->getList() : [];
		return (bool)($isAgreementsEnabled && count($agreementsList) > 0);
	}
}