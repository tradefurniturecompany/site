<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Block\Html;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\View\Element\Template;
use Amasty\MegaMenu\Model\Menu\TreeResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Topmenu extends Template implements IdentityInterface, ArgumentInterface
{
    const CHILD_CATEGORIES = '{{child_categories_content}}';

    const CHILD_CATEGORIES_PAGE_BUILDER = '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="ammega_menu_widget" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{child_categories_content}}</div></div></div>';

    const MAX_COLUMN_COUNT = 10;

    const DEFAULT_COLUMN_COUNT = 4;

    const CURRENT_CATEGORY_CLASS = 'current';

    /**
     * @var array
     */
    private $directivePatterns = [
        'construct' =>'/{{([a-z]{0,10})(.*?)}}/si'
    ];

    /**
     * @var TreeResolver
     */
    private $treeResolver;

    /**
     * @var Node|null
     */
    private $menu = null;

    /**
     * @var \Magento\Widget\Model\Template\Filter
     */
    private $filter;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Amasty\MegaMenu\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * Topmenu constructor.
     * @param TreeResolver $treeResolver
     * @param Template\Context $context
     * @param \Magento\Widget\Model\Template\Filter $filter
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Amasty\MegaMenu\Helper\Config $config
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param array $data
     */
    public function __construct(
        TreeResolver $treeResolver,
        Template\Context $context,
        \Magento\Widget\Model\Template\Filter $filter,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\MegaMenu\Helper\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->treeResolver = $treeResolver;
        $this->filter = $filter;
        $this->moduleManager = $moduleManager;
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $html = '';
        if (!$this->isHamburgerEnabled()
            || in_array($this->getTemplate(), [
                'Amasty_MegaMenu::html/hamburger/topmenu.phtml',
                'Amasty_MegaMenu::html/hamburger/leftmenu.phtml'
            ])
        ) {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return Node
     */
    public function getMenuTree()
    {
        if ($this->menu === null) {
            $this->menu = $this->treeResolver->get(
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->menu;
    }

    /**
     * @return array
     */
    public function getHamburgerMainNodes()
    {
        $mainNodes = [];
        foreach ($this->getMenuTree()->getChildren() as $node) {
            $mainNodes[] = $node;
        }

        return $mainNodes;
    }

    /**
     * @return array|Node\Collection
     */
    public function getMainNodes()
    {
        if ($this->isHamburgerEnabled()) {
            $mainNodes = [];
            foreach ($this->getMenuTree()->getChildren() as $node) {
                if ($node->getData('is_category')) {
                    continue;
                }
                $mainNodes[] = $node;
            }

            return $mainNodes;
        }

        return $this->getMenuTree()->getChildren();
    }

    /**
     * @return bool
     */
    public function isHamburgerEnabled()
    {
        return $this->config->isHamburgerEnabled();
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    public function getWidth($node)
    {
        $width = $node->getData(ItemInterface::WIDTH);
        if ($width === null) {
            $width = \Amasty\MegaMenu\Model\OptionSource\MenuWidth::FULL;
        }

        return $width ? 'auto' : 'full';
    }

    /**
     * @param $node
     *
     * @return float
     */
    public function getWidthValue($node)
    {
        $widthMode = $node->getData(ItemInterface::WIDTH);
        $result = 0;
        if ($widthMode == \Amasty\MegaMenu\Model\OptionSource\MenuWidth::CUSTOM) {
            $result = (float)$node->getData(ItemInterface::WIDTH_VALUE);
        }

        return $result;
    }


    /**
     * @param Node $node
     *
     * @return string
     */
    public function getCategoriesHtml(Node $node)
    {
        $html = '';
        if ($node->hasChildren()) {
            $html .= '<div class="ammenu-categories-container ammenu-categories">' . $this->getTreeHtml($node) . '</div>';
        }

        return $html;
    }

    /**
     * @param Node $node
     * @param int $level
     *
     * @return string
     */
    public function getTreeHtml(Node $node, $level = 1)
    {
        $html = '';
        if ($node->hasChildren()) {
            $nodesCount = $node->getChildren()->count();
            $columnCount = $this->getColumnCount($node) ?: $nodesCount;
            $widthClass = ($level == 1) ? (' -col-'. $columnCount) : '';
            $html .= '<ul class="ammenu-item -child' . $widthClass . '" '. ($level >= 3 ? ' style="display: none;"' : '') .'>';
            if ($level == 1 && $nodesCount > $columnCount) {
                $newColumnIndexes = $this->getNewColumnIndexes($nodesCount, $columnCount);
            }

            /** @var Node $childNode */
            $counter = 1;
            foreach ($node->getChildren() as $childNode) {
                if ($level === 1
                    && $counter !== 1
                    && ($nodesCount <= $columnCount || in_array($counter - 1, $newColumnIndexes))
                ) {
                    $html .= '</ul><ul class="ammenu-item -child' . $widthClass . '">';
                }
                $parentDataAttr = ($childNode->hasChildren() && $level > 1) ? 'data-ammenu-js="parent-subitem"' : '';
                $url = $childNode->getUrl() ? : '#';
                $html .= sprintf(
                    '<li class="ammenu-wrapper" '. $parentDataAttr . '><a href="%s" title="%s"
                    class="ammenu-link -level%s %s">
                    <span class="ammenu-wrapper">%s</span>
                    <span class="ammenu-arrow' . ($parentDataAttr != '' ? ' ammenu-icon -small -down' : '') . '"
                          data-ammenu-js="submenu-toggle"></span>
                    </a>',
                    $this->escapeUrl($url),
                    $this->escapeHtml($childNode->getName()),
                    $level,
                    $this->getHighLightClass($childNode),
                    $this->escapeHtml($childNode->getName())
                );

                if ($childNode->hasChildren()) {
                    $html .= $this->getTreeHtml($childNode, $level + 1);
                }

                $html .= '</li>';
                $counter++;
            }
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * @param Node $node
     *
     * @return int
     */
    private function getColumnCount(Node $node)
    {
        $count = $node->getColumnCount() !== null ? $node->getColumnCount() : self::DEFAULT_COLUMN_COUNT;
        $count = ($count <= self::MAX_COLUMN_COUNT) ? $count : self::MAX_COLUMN_COUNT;

        return $count;
    }

    /**
     * @param int $nodesCount
     * @param int $columnsCount
     *
     * @return array
     */
    public function getNewColumnIndexes($nodesCount, $columnsCount)
    {
        $itemPerPage = ceil($nodesCount / $columnsCount) ? : 1;
        $counter = 1;
        $tempMatrix = [];
        for ($i = 0; $i <= $itemPerPage; $i++) {
            for ($j = 0; $j < $columnsCount; $j++) {
                if ($counter <= $nodesCount) {
                    $tempMatrix[$i][$j] = $counter++;
                } else {
                    break;
                }
            }
        }

        // matrix transposition
        $tempMatrix = array_map(null, ...$tempMatrix);

        $result = [];
        foreach ($tempMatrix as $key => $value) {
            if (is_array($value)) {
                $value = array_filter($value); // remove null elements
            }
            $result[] = end($result) + count($value);
        }

        return $result;
    }


    /**
     * @return int
     */
    public function getStickyState()
    {
        return (int)$this->config->getModuleConfig('general/sticky');
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    public function getContent(Node $node)
    {
        $main = $node->getData(ItemInterface::CONTENT);
        if ($node->getIsCategory() && $main === null) {
            // set default value
            if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
                $main = self::CHILD_CATEGORIES_PAGE_BUILDER;
            } else {
                $main = self::CHILD_CATEGORIES;
            }

        }

        return $this->parseContent($node, $main);
    }

    /**
     * @param Node $node
     * @param $content
     *
     * @return string
     */
    private function parseContent(Node $node, $content)
    {
        if ($content) {
            if ($content !== self::CHILD_CATEGORIES && $this->isDirectivesExists($content)) {
                $content = $this->parseWysiwyg($content);
            }
            $content = $this->parseVariables($node, $content);
        }

        return $content;
    }

    /**
     * @param Node $node
     * @param $content
     *
     * @return string
     */
    private function parseVariables(Node $node, $content)
    {
        preg_match_all('@\{{(.+?)\}}@', $content, $matches);
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $result = '';
                switch ($match) {
                    case 'child_categories_content':
                        $result = $this->getCategoriesHtml($node);
                        break;
                }

                $content = str_replace('{{' . $match . '}}', $result, $content);
            }
        }

        return $content;
    }

    /**
     * @param $content
     *
     * @return string
     */
    private function parseWysiwyg($content)
    {
        $content = $this->filter->filter($content);
        return $content;
    }

    /**
     * Check if string has directives
     *
     * @param string $html
     * @return bool
     */
    private function isDirectivesExists($html)
    {
        $matches = false;
        foreach ($this->directivePatterns as $pattern) {
            if (preg_match($pattern, $html)) {
                $matches = true;
                break;
            }
        }
        return $matches;
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [\Amasty\MegaMenu\Model\Menu\Item::CACHE_TAG];
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        return $this->customerSession->getData();
    }

    /**
     * @return string
     */
    public function getCustomerFullName()
    {
        $session = $this->customerSession;

        if ($session->isLoggedIn()) {
            $customer = $session->getCustomer();
            return $customer->getFirstname() . ' ' . $customer->getLastname();
        }

        return false;
    }

    /**
     * @param Node $node
     * @return string
     */
    public function getHighLightClass($node)
    {
        return ($node->getData('has_active') || $node->getData('is_active')) ? self::CURRENT_CATEGORY_CLASS : '';
    }
}
