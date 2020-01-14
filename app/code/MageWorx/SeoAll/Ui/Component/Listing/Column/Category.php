<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Ui\Component\Listing\Column;

class Category extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\Source\Category
     */
    protected $categoryOptions;

    /**
     * @var string
     */
    protected $targetField;

    /**
     * @var boolean
     */
    protected $showTitleForUnknownCategory;

    /**
     * Constructor
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
        \MageWorx\SeoAll\Model\Source\Category $categoryOptions,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        $showTitleForUnknownCategory = true,
        $targetField = 'category_id',
        array $components = [],
        array $data = []
    ) {
        $this->categoryOptions = $categoryOptions;
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
        $categoryOptions = $this->categoryOptions->toArray();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!isset($item[$this->targetField])) {
                    continue;
                }

                if (!empty($categoryOptions[$item[$this->targetField]])) {
                    $item[$this->getData('name')] =
                        str_replace('(ID#', '<br> (ID#', $categoryOptions[$item[$this->targetField]]);
                } else if ($this->showTitleForUnknownCategory) {
                    $item[$this->getData('name')] = __('UNKNOWN CATEGORY') . ' (ID#' . $item[$this->targetField] . ')';
                }
            }
        }
        return $dataSource;
    }
}
