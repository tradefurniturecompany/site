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

namespace Mageplaza\Osc\Model;

use Exception;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Cms\Block\Block;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\UrlInterface;
use Magento\GiftMessage\Model\CompositeConfigProvider;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Osc\Helper\Data as OscHelper;
use Mageplaza\Osc\Model\System\Config\Backend\SealBlockImage;
use Mageplaza\Osc\Model\System\Config\Source\AllowGuestCheckout;
use Zend_Serializer_Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @type ShippingMethodManagementInterface
     */
    protected $shippingMethodManagement;

    /**
     * @var \Magento\Checkout\Model\CompositeConfigProvider
     */
    protected $giftMessageConfigProvider;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * @var QuoteItemRepository
     */
    protected $quoteItemRepository;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var Block
     */
    protected $cmsBlock;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param CompositeConfigProvider $configProvider
     * @param QuoteItemRepository $quoteItemRepository
     * @param StockRegistryInterface $stockRegistry
     * @param ModuleManager $moduleManager
     * @param OscHelper $oscHelper
     * @param Block $cmsBlock
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        PaymentMethodManagementInterface $paymentMethodManagement,
        ShippingMethodManagementInterface $shippingMethodManagement,
        CompositeConfigProvider $configProvider,
        QuoteItemRepository $quoteItemRepository,
        StockRegistryInterface $stockRegistry,
        ModuleManager $moduleManager,
        OscHelper $oscHelper,
        Block $cmsBlock,
        StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->giftMessageConfigProvider = $configProvider;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->stockRegistry = $stockRegistry;
        $this->moduleManager = $moduleManager;
        $this->_oscHelper = $oscHelper;
        $this->cmsBlock = $cmsBlock;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        if (!$this->_oscHelper->isOscPage()) {
            return [];
        }

        $output = [
            'shippingMethods'       => $this->getShippingMethods(),
            'selectedShippingRate'  => !empty($existShippingMethod = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingMethod())
                ? $existShippingMethod : $this->_oscHelper->getDefaultShippingMethod(),
            'paymentMethods'        => $this->getPaymentMethods(),
            'selectedPaymentMethod' => $this->_oscHelper->getDefaultPaymentMethod(),
            'oscConfig'             => $this->getOscConfig(),
            'checkVersion'          => $this->_oscHelper->checkVersion()
        ];

        return $output;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    private function getOscConfig()
    {
        return [
            'addressFields'           => $this->_oscHelper->getAddressHelper()->getAddressFields(),
            'autocomplete'            => [
                'type'                   => $this->_oscHelper->getAutoDetectedAddress(),
                'google_default_country' => $this->_oscHelper->getGoogleSpecificCountry(),
            ],
            'register'                => [
                'dataPasswordMinLength'        => $this->_oscHelper->getConfigValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH),
                'dataPasswordMinCharacterSets' => $this->_oscHelper->getConfigValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER)
            ],
            'allowGuestCheckout'      => $this->allowGuestCheckout(),
            'showBillingAddress'      => $this->_oscHelper->getShowBillingAddress(),
            'newsletterDefault'       => $this->_oscHelper->isSubscribedByDefault(),
            'isUsedGiftWrap'          => (bool) $this->checkoutSession->getQuote()->getShippingAddress()->getUsedGiftWrap(),
            'giftMessageOptions'      => array_merge_recursive($this->giftMessageConfigProvider->getConfig(), [
                'isEnableOscGiftMessageItems' => $this->_oscHelper->isEnableGiftMessageItems()
            ]),
            'isDisplaySocialLogin'    => $this->isDisplaySocialLogin(),
            'isUsedMaterialDesign'    => $this->_oscHelper->isUsedMaterialDesign(),
            'isAmazonAccountLoggedIn' => false,
            'geoIpOptions'            => [
                'isEnableGeoIp' => $this->_oscHelper->getAddressHelper()->isEnableGeoIP(),
                'geoIpData'     => $this->_oscHelper->getAddressHelper()->getGeoIpData()
            ],
            'compatible'              => [
                'isEnableModulePostNL' => $this->_oscHelper->isEnableModulePostNL(),
            ],
            'show_toc'                => $this->_oscHelper->getShowTOC(),
            'qtyIncrements'           => $this->getItemQtyIncrement(),
            'sealBlock'               => $this->getSealBlock(),
            'isShowItemListToggle'    => $this->_oscHelper->isShowItemListToggle()
        ];
    }

    /**
     * @return bool
     */
    private function allowGuestCheckout()
    {
        $allow = $this->_oscHelper->getAllowGuestCheckout($this->checkoutSession->getQuote());

        return (bool) ((int) $allow !== AllowGuestCheckout::REQUIRE_CREATE_ACCOUNT);
    }

    /**
     * Return array of static blocks
     *
     * @return string
     * @throws LocalizedException
     */
    public function getSealBlock()
    {
        $sealContent = '';

        if ($this->_oscHelper->isEnabledSealBlock() == 1) {
            $blockId = $this->_oscHelper->getSealStaticBlock();
            $sealContent = $this->cmsBlock->setBlockId($blockId)->toHtml();
        } else {
            if ($this->_oscHelper->isEnabledSealBlock() == 2) {
                $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $sealImage = $mediaUrl . SealBlockImage::UPLOAD_DIR . $this->_oscHelper->getSealImage();
                $sealDescription = $this->_oscHelper->getSealDescription();

                $sealContent = '<img src="' . $sealImage . '"><p>' . $sealDescription . '</p>';
            }
        }

        return $sealContent;
    }

    /**
     * Returns array of payment methods
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getPaymentMethods()
    {
        $paymentMethods = [];
        $quote = $this->checkoutSession->getQuote();
        if (!$quote->getIsVirtual()) {
            foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
                $paymentMethods[] = [
                    'code'  => $paymentMethod->getCode(),
                    'title' => $paymentMethod->getTitle()
                ];
            }
        }

        return $paymentMethods;
    }

    /**
     * @return ShippingMethodInterface[]
     * @throws NoSuchEntityException
     * @throws StateException
     */
    private function getShippingMethods()
    {
        $methodLists = $this->shippingMethodManagement->getList($this->checkoutSession->getQuote()->getId());
        foreach ($methodLists as $key => $method) {
            $methodLists[$key] = $method->__toArray();
        }

        return $methodLists;
    }

    /**
     * Retrieve quote item data
     *
     * @return array
     */
    private function getItemQtyIncrement()
    {
        $itemQty = [];

        try {
            $quoteId = $this->checkoutSession->getQuote()->getId();
            if ($quoteId) {
                /** @var array $quoteItems */
                $quoteItems = $this->quoteItemRepository->getList($quoteId);

                /** @var Item $item */
                foreach ($quoteItems as $item) {
                    $stockItem = $this->stockRegistry->getStockItem(
                        $item->getProduct()->getId(),
                        $item->getStore()->getWebsiteId()
                    );
                    if ($stockItem->getEnableQtyIncrements() && $stockItem->getQtyIncrements()) {
                        $itemQty[$item->getId()] = $stockItem->getQtyIncrements() ?: 1;
                    }
                }
            }
        } catch (Exception $e) {
            $itemQty = [];
        }

        return $itemQty;
    }

    /**
     * @return bool
     */
    private function isDisplaySocialLogin()
    {
        return $this->moduleManager->isOutputEnabled('Mageplaza_SocialLogin') && !$this->_oscHelper->isDisabledSocialLoginOnCheckout();
    }
}
