<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Block\Html\Topmenu;
use Amasty\MegaMenu\Ui\DataProvider\Form\Link\DataProvider;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class Level implements ModifierInterface
{
    private $entity;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        Registry $registry,
        \Magento\Framework\Module\Manager $moduleManager,
        RequestInterface $request
    ) {
        $this->storeId = (int)$request->getParam('store', 0);
        $this->entity = $registry->registry('current_category');
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->entity && $this->entity->getLevel() == 2) {
            $itemsToHide = ['category_level_error'];
        } else {
            $itemsToHide = ['width', 'column_count', 'label', 'label_group', 'content', 'label_group'];
        }

        foreach ($itemsToHide as $item) {
            $meta['am_mega_menu_fieldset']['children'][$item]['arguments']['data']['config']['visible'] = false;
        }

        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default'] = Topmenu::CHILD_CATEGORIES_PAGE_BUILDER;
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
                = __('You can use the menu item Add Content for showing child categories.');
        } else {
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default'] = Topmenu::CHILD_CATEGORIES;
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['component'] = 'Amasty_MegaMenu/js/form/element/wysiwyg';
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
                = __('You can use the variable: {{child_categories_content}} for showing child categories.');
        }

        return $meta;
    }
}
