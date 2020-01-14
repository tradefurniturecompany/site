<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Block;

use Amasty\MegaMenu\Model\Menu\TreeResolver;
use Magento\Framework\View\Element\Template;

class Toggle extends Template
{

    /**
     * @var \Amasty\MegaMenu\Helper\Config
     */
    private $config;

    public function __construct(
        Template\Context $context,
        \Amasty\MegaMenu\Helper\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    public function isHamburgerEnabled()
    {
        return $this->config->isHamburgerEnabled();
    }
}
