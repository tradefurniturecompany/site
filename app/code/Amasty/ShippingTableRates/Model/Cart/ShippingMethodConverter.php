<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Cart;

use Amasty\ShippingTableRates\Helper\Data;
use Amasty\ShippingTableRates\Model\MethodFactory;
use Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Plugin preparing and adding comments to Magento Shipping Method
 */
class ShippingMethodConverter
{
    /**
     * @var CollectionFactory
     */
    private $labelCollectionFactory;
    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var ExtensionAttributesFactory
     */
    private $attributesFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CollectionFactory $labelCollectionFactory,
        MethodFactory $methodFactory,
        Data $helperData,
        ExtensionAttributesFactory $attributesFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->methodFactory = $methodFactory;
        $this->helperData = $helperData;
        $this->attributesFactory = $attributesFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface $result$
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface
     */
    public function afterModelToDataObject(\Magento\Quote\Model\Cart\ShippingMethodConverter $subject, $result)
    {
        if ($result->getCarrierCode() == 'amstrates') {
            $methodId = str_replace('amstrates', '', $result->getMethodCode());
            $storeId = $this->storeManager->getStore()->getId();
            /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Label\Collection $label */
            $label = $this->labelCollectionFactory->create()
                ->addFiltersByMethodIdStoreId($methodId, $storeId)
                ->getLastItem();
            /** @var \Amasty\ShippingTableRates\Model\Method $method */
            $method = $this->methodFactory->create()->load($methodId);
            $comment = $label->getComment() != "" ? $label->getComment() : $method->getComment();
            $comment = $this->helperData->escapeHtml($comment);
            if ($comment) {
                if ($img = $method->getCommentImg()) {
                    $imgUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $img;
                    $comment = str_replace('{IMG}', '<img src="' . $imgUrl . '" />', $comment);
                }

                $extAttributes = $result->getExtensionAttributes();
                if ($extAttributes === null) {
                    $extAttributes = $this->attributesFactory
                        ->create(\Magento\Quote\Api\Data\ShippingMethodInterface::class);
                }
                $extAttributes->setAmstartesComment(__($comment));
                $result->setExtensionAttributes($extAttributes);
            }
        }

        return $result;
    }
}
