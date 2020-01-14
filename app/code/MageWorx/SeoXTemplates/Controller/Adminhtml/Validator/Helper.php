<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Validator;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;

class Helper
{
    /**
     * @var bool
     */
    protected $isValidByStoreMode = true;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Helper constructor.
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        $this->storeManager   = $storeManager;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @return $this
     */
    public function validate(\MageWorx\SeoXTemplates\Model\AbstractTemplate $template)
    {
        if ($this->storeManager->isSingleStoreMode() && !$template->getIsSingleStoreMode()) {

            $this->isValidByStoreMode = false;

            $this->messageManager->addError(
                __('Template can be used if Single Store Mode is disabled only.')
            );

        } elseif (!$this->storeManager->isSingleStoreMode() && $template->getIsSingleStoreMode()) {

            $this->isValidByStoreMode = false;

            $this->messageManager->addError(
                __('Template can be used if Single Store Mode is enabled only.')
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValidByStoreMode()
    {
        return $this->isValidByStoreMode;
    }
}
