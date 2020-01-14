<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Controller\Index;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Controller\Onepage;
use Magento\Checkout\Model\Cart;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\LayoutFactory as ResultLayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\SalesRule\Model\Coupon;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Osc\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class Index
 * @package Mageplaza\Osc\Controller\Index
 */
class Index extends Onepage
{
    /**
     * @type Data
     */
    protected $_checkoutHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $allowCollectTotals = false;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param Registry $coreRegistry
     * @param InlineInterface $translateInline
     * @param Validator $formKeyValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param LayoutFactory $layoutFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param PageFactory $resultPageFactory
     * @param ResultLayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Cart $cart
     * @param LoggerInterface $logger
     * @param Configurable $configurable
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Registry $coreRegistry,
        InlineInterface $translateInline,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        LayoutFactory $layoutFactory,
        CartRepositoryInterface $quoteRepository,
        PageFactory $resultPageFactory,
        ResultLayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        Cart $cart,
        LoggerInterface $logger,
        Configurable $configurable
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
        $this->logger = $logger;
        $this->configurable = $configurable;

        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * @return ResponseInterface|Redirect|
     * \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->_checkoutHelper = $this->_objectManager->get(Data::class);
        if (!$this->_checkoutHelper->isEnabled()) {
            $this->messageManager->addError(__('One step checkout is turned off.'));

            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        $quote = $this->getOnepage()->getQuote();

        if (!$this->_customerSession->isLoggedIn() && !$this->_checkoutHelper->getAllowGuestCheckout($quote)) {
            $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $redirectPath = $this->addProductCoupon($quote);
        if ($redirectPath) {
            return $this->resultRedirectFactory->create()->setPath($redirectPath);
        }

        // generate session ID only if connection is unsecure according to issues in session_regenerate_id function.
        // @see http://php.net/manual/en/function.session-regenerate-id.php
        if (!$this->isSecureRequest()) {
            $this->_customerSession->regenerateId();
        }
        $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        $this->initDefaultMethods($quote);

        $resultPage = $this->resultPageFactory->create();
        $checkoutTitle = $this->_checkoutHelper->getCheckoutTitle();
        $resultPage->getConfig()->getTitle()->set($checkoutTitle);
        $resultPage->getConfig()->setPageLayout($this->_checkoutHelper->isShowHeaderFooter() ? '1column' : 'checkout');

        return $resultPage;
    }

    /**
     * @param Quote $quote
     *
     * @return string|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function addProductCoupon($quote)
    {
        $reload = false;

        if ($skuArray = $this->getRequest()->getParam('sku')) {
            $this->addProductOsc($skuArray);
            $reload = true;
        }

        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return 'checkout/cart';
        }

        if ($coupon = $this->getRequest()->getParam('coupon')) {
            $this->setCouponCodeOsc($quote, $coupon);
            $reload = true;
        }

        if ($reload) {
            return $this->_checkoutHelper->getOscRoute();
        }

        return null;
    }

    /**
     * Default shipping/payment method
     *
     * @param Quote $quote
     *
     * @return bool
     * @throws Exception
     */
    public function initDefaultMethods(Quote $quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            $shippingAddress->setCountryId($this->_checkoutHelper->getDefaultCountryId())
                ->save();
        }

        try {
            $shippingAddress->setCollectShippingRates(true);

            /** @var TotalsCollector $totalsCollector */
            $totalsCollector = $this->_objectManager->get(TotalsCollector::class);
            $totalsCollector->collectAddressTotals($quote, $shippingAddress);

            $availableMethods = $this->_objectManager->get(ShippingMethodManagementInterface::class)
                ->getList($quote->getId());

            $method = null;
            if (sizeof($availableMethods) == 1) {
                $method = array_shift($availableMethods);
            } else {
                if (!$shippingAddress->getShippingMethod() && sizeof($availableMethods)) {
                    $defaultMethod = array_filter($availableMethods, [$this, 'filterMethod']);
                    if (sizeof($defaultMethod)) {
                        $method = array_shift($defaultMethod);
                    }
                }
            }

            if ($method) {
                $methodCode = $method->getCarrierCode() . '_' . $method->getMethodCode();
                $this->getOnepage()->saveShippingMethod($methodCode);
            }
            $this->quoteRepository->save($quote);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $method
     *
     * @return bool
     */
    public function filterMethod($method)
    {
        $defaultShippingMethod = $this->_checkoutHelper->getDefaultShippingMethod();
        $methodCode = $method->getCarrierCode() . '_' . $method->getMethodCode();
        if ($methodCode === $defaultShippingMethod) {
            return true;
        }

        return false;
    }

    /**
     * @param $quote
     * @param $coupon
     */
    public function setCouponCodeOsc($quote, $coupon)
    {
        $couponModel = $this->_objectManager->get(Coupon::class);
        $session = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $couponCode = trim($coupon);
        $oldCouponCode = $quote->getCouponCode();
        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return;
        }

        $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;
        $itemsCount = $quote->getItemsCount();
        if ($itemsCount) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
            $this->quoteRepository->save($quote);
        }
        if ($codeLength) {
            $coupon = $couponModel->load($couponCode, 'code');
            if (!$itemsCount && $isCodeLengthValid && $coupon->getId()) {
                $session->getQuote()->setCouponCode($couponCode)->save();
            }
        }
    }

    /**
     * @param $skuArray
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addProductOsc($skuArray)
    {
        $storeId = $this->storeManager->getStore()->getId();
        foreach ($skuArray as $sku => $qty) {
            try {
                $product = $this->productRepository->get($sku, false, $storeId, true);
                if ($product && $product->getExtensionAttributes()->getStockItem()->getIsInStock()) {
                    $configurableProductId = $this->configurable->getParentIdsByChild($product->getId());
                    if ($configurableProductId) {
                        $productParent = $this->productRepository->getById($configurableProductId[0]);
                        $attributes = $productParent->getTypeInstance(true)->getConfigurableAttributesAsArray($productParent);
                        $supperAttribute = [];
                        foreach ($attributes as $attribute) {
                            $supperAttribute[$attribute['attribute_id']] = $product->getData($attribute['attribute_code']);
                        }
                        $requestInfo = [];
                        $requestInfo['product'] = $configurableProductId[0];
                        $requestInfo['super_attribute'] = $supperAttribute;
                        $requestInfo['qty'] = $qty;

                        $this->cart->addProduct($productParent, $requestInfo);
                    } else {
                        $this->cart->addProduct($product, $qty);
                    }

                    $this->cart->save();
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Requested %1 product doesn\'t exist', $sku));
                $this->logger->critical($e->getMessage());
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest()
    {
        $request = $this->getRequest();
        $referrer = $request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }
}
