<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Ui\DataProvider\Form\Link\Modifier;

use Amasty\MegaMenu\Ui\DataProvider\Form\Link\DataProvider;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class UseDefault implements ModifierInterface
{
    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    public function __construct(
        Registry $registry,
        RequestInterface $request,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
        $this->storeId = (int)$request->getParam('store', 0);
        if ($registry->registry(LinkInterface::PERSIST_NAME)) {
            $this->entityId = $registry->registry(LinkInterface::PERSIST_NAME)->getEntityId();
            $this->type = 'custom';
        } else {
            $this->entityId = $registry->registry('current_category')->getId();
            $this->type = 'category';
        }
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $defaultData = $this->getItem() ? $this->getItem()->getData() : [];
        $perStoreData = [];
        if ($this->storeId) {
            $perStoreData = $this->getItem($this->storeId) ? $this->getItem($this->storeId)->getData() : [];
        }
        $notnull = function ($var) {
            return $var !== null;
        };
        $changedData = array_merge(
            array_filter($defaultData, $notnull),
            array_filter($perStoreData, $notnull)
        );

        $fieldsToUse = ItemInterface::FIELDS_BY_STORE_CATEGORY['am_mega_menu_fieldset'];
        foreach ($changedData as $field => $value) {
            if (in_array($field, $fieldsToUse)) {
                $data[$changedData['entity_id']][$field] = $value;
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->isShowDefaultCheckbox()) {
            $fieldsByStore = $this->type == 'category' ?
                ItemInterface::FIELDS_BY_STORE_CATEGORY :
                ItemInterface::FIELDS_BY_STORE_CUSTOM;
            foreach ($fieldsByStore as $fieldSetCode => $fieldSet) {
                foreach ($fieldSet as $field) {
                    $meta[$fieldSetCode]['children'][$field]['arguments']['data']['config']['service'] =
                        [
                            'template' => 'ui/form/element/helper/service'
                        ];
                    if (!$this->getItem($this->storeId)
                        || $this->getItem($this->storeId)->getData($field) === null
                    ) {
                        $meta[$fieldSetCode]['children'][$field]['arguments']['data']['config']['disabled'] = true;
                    }
                }
            }
        }

        return $meta;
    }

    /**
     * @return bool
     */
    private function isShowDefaultCheckbox()
    {
        return (bool)$this->storeId;
    }

    /**
     * @param int $storeId
     *
     * @return ItemInterface
     */
    private function getItem($storeId = 0)
    {
        $item = $this->itemRepository->getByEntityId($this->entityId, $storeId, $this->type);
        if ($item && $item->getType() === 'category' && $item->getContent() === null) {
            $item->setContent('{{child_categories_content}}'); // set default value
        }

        return $item;
    }
}
