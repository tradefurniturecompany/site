<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Model;

class SetupInitiator
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $data;

    /**
     * SetupInitiator constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->data          = $data;
    }

    /**
     * @param string $key
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function call($key, $setup)
    {
        if (!empty($this->data['listeners'])) {
            foreach ($this->data['listeners'] as $upgradeClassName) {
                $upgradeClass = $this->objectManager->get($upgradeClassName);
                $upgradeClass->setupForRelations($key, $setup);
            }
        }
    }
}