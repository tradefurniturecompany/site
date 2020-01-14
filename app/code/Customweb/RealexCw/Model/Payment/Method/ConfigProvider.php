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

namespace Customweb\RealexCw\Model\Payment\Method;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
	/**
	 * @var \Magento\Framework\View\Asset\Repository
	 */
	protected $_assetRepo;

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;

    /**
     * @var AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod[]
     */
    protected $authorizationMethods = [];

	/**
	 * @param \Magento\Framework\View\Asset\Repository $assetRepo
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Framework\UrlInterface $urlBuilder
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Payment\Helper\Data $paymentHelper
	 * @param \Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 */
    public function __construct(
   		\Magento\Framework\View\Asset\Repository $assetRepo,
   		\Magento\Framework\App\RequestInterface $request,
    	\Magento\Framework\UrlInterface $urlBuilder,
    	\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Payment\Helper\Data $paymentHelper,
    	\Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
    ) {
    	$this->_assetRepo = $assetRepo;
    	$this->_request = $request;
    	$this->_urlBuilder = $urlBuilder;
    	$this->_checkoutSession = $checkoutSession;
    	$this->_authorizationMethodFactory = $authorizationMethodFactory;
        foreach (array_keys($paymentHelper->getPaymentMethods()) as $code) {
        	if (strpos($code, 'realexcw_') === 0) {
	        	$this->methods[$code] = $paymentHelper->getMethodInstance($code);
        	}
        }
    }

    public function getConfig()
    {
        $config = [];
        foreach ($this->methods as $method) {
            if ($method->isActive()) {
                $config['payment']['show_image'][$method->getCode()] = $method->isShowImage();
                $config['payment']['image_url'][$method->getCode()] = $this->getImageUrl($method);
                $config['payment']['description'][$method->getCode()] = nl2br($method->getDescription());
                $config['payment']['form'][$method->getCode()] = $this->getForm($method);
                $config['payment']['authorizationMethod'][$method->getCode()] = $this->getAuthorizationMethodAdapter($method)->getAdapter()->getAuthorizationMethodName();
                $config['payment']['failureMessage'][$method->getCode()] = $this->getFailureMessage($method);
            }
        }
        return $config;
    }

    /**
     * Retrieve a payment method's rendered form elements.
     *
     * @param AbstractMethod $method
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $alias
     * @return string
     */
    public function getForm(AbstractMethod $method, \Magento\Quote\Model\Quote $quote = null, $alias = null)
    {
    	return $this->getFormRenderer($method)->renderElements($this->getAuthorizationMethodAdapter($method, $quote, $alias)->getVisibleFormFields(), $method->getCode());
    }

    /**
     * Retrieve the failure message if any.
     *
     * @param AbstractMethod $method
     * @return string|null
     */
    protected function getFailureMessage(AbstractMethod $method)
    {
    	if ($this->_checkoutSession->getQuote()->getPayment()->getMethod() == $method->getCode()) {
	    	$failureMessage = $this->_checkoutSession->getRealexCwFailureMessage();
	    	if (!empty($failureMessage)) {
	    		$this->_checkoutSession->unsRealexCwFailureMessage();
	    		return $failureMessage;
	    	}
    	}

    }

    /**
     * Retrieve the url of a payment method image file
     *
     * @param AbstractMethod $method
     * @return string
     */
    protected function getImageUrl(AbstractMethod $method)
    {
    	try {
    		$params = ['_secure' => $this->_request->isSecure()];
    		return $this->_assetRepo->getUrlWithParams('Customweb_RealexCw/images/payment/method/' . $method->getPaymentMethodName() . '.png', $params);
    	} catch (\Magento\Framework\Exception\LocalizedException $e) {
    		$this->_logger->critical($e);
    		return $this->_urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
    	}
    }

    /**
     * Retrieve a checkout form renderer.
     *
     * @param AbstractMethod $method
     * @return \Customweb_Form_IRenderer
     */
    protected function getFormRenderer(AbstractMethod $method)
    {
    	return new \Customweb\RealexCw\Model\Renderer\CheckoutForm($method->getCode());
    }

    /**
     * Retrieve an authorization method adapter.
     *
     * @param AbstractMethod $method
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $alias
     * @return \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod
     */
    protected function getAuthorizationMethodAdapter(AbstractMethod $method, \Magento\Quote\Model\Quote $quote = null, $alias = null)
    {
    	if (!array_key_exists($method->getCode(), $this->authorizationMethods)) {
	    	$context = $this->_authorizationMethodFactory->getContextFactory()->createQuote($method, $quote, $alias);
	    	$this->authorizationMethods[$method->getCode()] = $this->_authorizationMethodFactory->create($context);
    	}
    	return $this->authorizationMethods[$method->getCode()];
    }
}
