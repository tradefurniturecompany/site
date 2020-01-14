<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\DesignInterface;

class Config extends AbstractHelper
{
    const MODULE_PATH = 'ammegamenu/';

    const DEFAULT_BREAKPOINT = 1050;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    public function __construct(
        \Magento\Framework\Filter\FilterManager $filterManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->filterManager = $filterManager;
    }

    /**
     * @param $path
     * @param int $storeId
     *
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getModuleConfig('general/enabled');
    }

    public function isHamburgerEnabled()
    {
        return (bool)$this->getModuleConfig('general/hamburger_enabled');
    }
}
