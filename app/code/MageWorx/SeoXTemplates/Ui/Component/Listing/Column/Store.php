<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Ui\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store as UiStore;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\Escaper;

/**
 * Class Store
 */
class Store extends UiStore
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Store constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        $storeKey = 'store_id'
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $systemStore, $escaper, $components, $data, $storeKey);
    }
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        $origStores = $item['store_id'];

        $isSingleStoreMode = isset($item['is_single_store_mode']) ? $item['is_single_store_mode'] : false;
        $isUseForDefaultValue = isset($item['use_for_default_value']) ? $item['use_for_default_value'] : false;

        if (is_array($origStores) && empty($origStores)) {
            return '';
        }

        if (!is_array($origStores) && $origStores === '') {
            return '';
        }

        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        if (in_array('0', $origStores) && $isUseForDefaultValue) {
            return __('For Default Values');
        } elseif (in_array('0', $origStores) && count($origStores) == 1 && !$isSingleStoreMode) {
            return __('For each store');
        } elseif (in_array('0', $origStores) && count($origStores) == 1 && $isSingleStoreMode) {
            return __('Single-Store Mode');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = false;
        }
    }
}
