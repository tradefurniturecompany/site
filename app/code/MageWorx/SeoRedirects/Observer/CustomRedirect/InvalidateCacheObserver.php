<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Observer\CustomRedirect;

class InvalidateCacheObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;


    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->typeList = $typeList;
        $this->config   = $config;
    }

    /**
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $redirect = $observer->getDataObject();

        if (!$redirect) {
            return;
        }

        if (empty($redirect->getOrigData('redirect_id'))
            || (!$redirect->getOrigData('status') && $redirect->getData('status'))
        ) {
            if ($this->config->isEnabled()) {
                $this->typeList->invalidate('full_page');
            }

            return;
        }
    }
}
