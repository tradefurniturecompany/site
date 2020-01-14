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

namespace Mageplaza\Osc\Observer;

use Magento\Config\Model\ResourceModel\Config as ModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\GiftMessage\Helper\Message;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Osc\Helper\Data as OscHelper;

/**
 * Class OscConfigObserver
 * @package Mageplaza\Osc\Observer
 */
class OscConfigObserver implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ModelConfig
     */
    protected $_modelConfig;

    /**
     * @var MessageManager
     */
    protected $_messageManager;

    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * OscConfigObserver constructor.
     *
     * @param ModelConfig $modelConfig
     * @param MessageManager $messageManager
     * @param OscHelper $oscHelper
     */
    public function __construct(
        ModelConfig $modelConfig,
        MessageManager $messageManager,
        OscHelper $oscHelper
    ) {
        $this->_modelConfig = $modelConfig;
        $this->_messageManager = $messageManager;
        $this->_oscHelper = $oscHelper;
    }

    /**
     * @param Observer $observer
     *
     * @throws FileSystemException
     */
    public function execute(Observer $observer)
    {
        $scopeId = 0;
        $isGiftMessage = !$this->_oscHelper->isDisabledGiftMessage();
        $isGiftMessageItems = $this->_oscHelper->isEnableGiftMessageItems();
        $isEnableTOC = ($this->_oscHelper->disabledPaymentTOC() || $this->_oscHelper->disabledReviewTOC());
        $this->_modelConfig
            ->saveConfig(
                Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER,
                $isGiftMessage,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $scopeId
            )
            ->saveConfig(
                Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS,
                $isGiftMessageItems,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $scopeId
            )
            ->saveConfig(
                'checkout/options/enable_agreements',
                $isEnableTOC,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $scopeId
            );
    }
}
