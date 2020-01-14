<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Plugin\Porto\Block;

use Amasty\MegaMenu\Block\Toggle;
use Amasty\MegaMenu\Block\Html\Topmenu;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Template
 */
class Template
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $blockName
     * @return string
     */
    public function aroundGetChildHtml($subject, \Closure $proceed, $blockName)
    {
        $html = $proceed($blockName);
        if ($blockName == 'logo') {
            $toogleBlock = $this->layout->createBlock(Toggle::class, 'amasty.menu.toggle', [
                'data' => [
                    'template' => 'Amasty_MegaMenu::toggle.phtml',
                    'is_porto' => true
                ]
            ]);
            if ($toogleBlock) {
                $html = $toogleBlock->toHtml() . $html;
            }
        }

        if ($blockName == 'custom_block') {
            $hamburgerBLock = $this->layout->createBlock(Topmenu::class, 'catalog.topnav.hamburger', [
                'data' => [
                    'template' => 'Amasty_MegaMenu::html/hamburger/topmenu.phtml',
                    'is_porto' => true
                ]
            ]);
            if ($hamburgerBLock) {
                $html .= $hamburgerBLock->toHtml();
            }
        }

        return $html;
    }
}
