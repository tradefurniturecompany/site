<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Store\Ui\Component\Listing\Column\Store as ColumnStore;

class SourceStore extends ColumnStore
{
    /**
     * SourceStore constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        array $components = [],
        array $data = [],
        $storeKey = 'source_store_id'
    ) {
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
        $output = '';

        if (isset($item[$this->storeKey])) {
            $storeIds = $item[$this->storeKey];
        }

        if (!isset($storeIds)) {
            return '';
        }

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeIds) && count($storeIds) == 1) {
            return __('All Store Views');
        }

        $data = $this->systemStore->getStoresStructure(false, $storeIds);

        foreach ($data as $website) {
            $output .= $website['label'] . "<br/>";

            foreach ($website['children'] as $group) {
                $output .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";

                foreach ($group['children'] as $store) {
                    $output .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $output;
    }
}
