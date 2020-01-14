<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source;

use \Magento\Framework\Module\Manager as ModuleManager;

class AssociatedTypes
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     *
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ModuleManager $moduleManager
    ) {
    
        $this->moduleManager = $moduleManager;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
                    'label' => 'Bundle'
                ]
            ];

            if ($this->moduleManager->isEnabled('Magento_GroupedProduct')) {
                $this->options[] = [
                    'value' => \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
                    'label' => 'Grouped'
                ];
            }
            
            if ($this->moduleManager->isEnabled('Magento_ConfigurableProduct')) {
                $this->options[] = [
                    'value' => \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
                    'label' => 'Configurable'
                ];
            }
        }
        return $this->options;
    }
}
