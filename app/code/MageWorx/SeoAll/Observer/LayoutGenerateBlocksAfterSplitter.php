<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product\CollectionFactory;
use MageWorx\SeoXTemplates\Model\DynamicRenderer\Category as Renderer;

class LayoutGenerateBlocksAfterSplitter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * LayoutGenerateBlocksAfterSplitter constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->eventManager->dispatch(
            'layout_generate_blocks_after_one',
            [
                'object' => $this,
                'observer' => $observer,
                'full_action_name' => $observer->getFullActionName()
            ]
        );

        $this->eventManager->dispatch(
            'layout_generate_blocks_after_two',
            [
                'object' => $this,
                'observer' => $observer,
                'full_action_name' => $observer->getFullActionName()
            ]
        );

        $this->eventManager->dispatch(
            'layout_generate_blocks_after_three',
            [
                'object' => $this,
                'observer' => $observer,
                'full_action_name' => $observer->getFullActionName()
            ]
        );
    }
}
