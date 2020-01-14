<?php

namespace Interactivated\Sale\Plugin;

class Collection
{
    public function aroundAddCategoryFilter($collection, $closure, \Magento\Catalog\Model\Category $category)
    {
        if ($category->getEntityId() == '1559') {
            if (!$category->getData('sale_filter_added')) {
                $collection->addAttributeToFilter(
                    'special_from_date',
                    ['notnull' => true],
                    'left'
                );
                $collection->getSelect()
                    ->where('price_index.final_price < price_index.price');
                $category->getData('sale_filter_added', true);
            }
        } else {
            return $closure($category);
        }

        return $closure($category);
    }
}