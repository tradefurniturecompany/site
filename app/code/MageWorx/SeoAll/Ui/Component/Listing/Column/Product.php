<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Ui\Component\Listing\Column;

class Product extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var array|null
     */
    protected $productNames;

    /**
     * @var string
     */
    protected $targetField;

    /**
     * @var boolean
     */
    protected $showTitleForUnknownCategory;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param boolean $showTitleForUnknownCategory
     * @param string $targetField
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        $showTitleForUnknownCategory = true,
        $targetField = 'product_sku',
        array $components = [],
        array $data = []

    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->showTitleForUnknownCategory = $showTitleForUnknownCategory;
        $this->targetField = $targetField;

        if (!empty($data['targetField'])) {
            $this->targetField = $data['targetField'];
        }
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $productsSku  = [];

            foreach ($dataSource['data']['items'] as & $item) {
                $productsSku[] = $item[$this->targetField];
            }
            foreach ($dataSource['data']['items'] as & $item) {
                $getProductNameOptions = $this->getProductOptions($productsSku);
                if (!empty($getProductNameOptions[$item[$this->targetField]])) {
                    $item[$this->getData('name')] =
                        $getProductNameOptions[$item[$this->targetField]] . "<br>" . ' (SKU#' . $item[$this->targetField] . ')';
                } elseif ($this->showTitleForUnknownCategory) {
                    $item[$this->getData('name')] =
                        __('UNKNOWN PRODUCT') . "<br>" .' (SKU#' . $item[$this->targetField] . ')';
                }
            }
        }
    return $dataSource;
    }

    /**
     * @param array $productsSku
     * @return array
     */
    protected function getProductOptions(array $productsSku)
    {
        if ($this->productNames === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToFilter('sku', $productsSku);
            $collection->addAttributeToSelect('name');

            foreach ($collection as $product) {
                $this->productNames[$product->getData('sku')] = $product->getData('name');
            }
        }
        return $this->productNames;
    }
}
