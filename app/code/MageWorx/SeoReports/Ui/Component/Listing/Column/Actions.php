<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Ui\Component\Listing\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $editUrl;

    /**
     * @var string
     */
    protected $idForEdit;

    /**
     * @var bool
     */
    protected $useStoreForEditUrl;

    /**
     * Actions constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param string $editUrl
     * @param string $idForEdit
     * @param bool $useStoreForEditUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        $editUrl,
        $idForEdit = 'id',
        $useStoreForEditUrl = true,
        array $components = [],
        array $data = []
    ) {
        $this->editUrl            = $editUrl;
        $this->storeManager       = $storeManager;
        $this->urlBuilder         = $urlBuilder;
        $this->idForEdit          = $idForEdit;
        $this->useStoreForEditUrl = $useStoreForEditUrl;
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
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as & $item) {

            if (!isset($item['entity_id'])) {
                continue;
            }

            if ($this->editUrl) {

                $params = [
                    $this->idForEdit => $item['reference_id'],
                ];

                if ($this->useStoreForEditUrl) {
                    $params['store'] = $this->getStoreId($item);
                }

                $item[$this->getData('name')]['edit'] = [
                    'href'   => $this->urlBuilder->getUrl(
                        $this->editUrl,
                        $params
                    ),
                    'label'  => __('Edit'),
                    'target' => '_blank'
                ];
            }

            $item[$this->getData('name')]['view'] = [
                'href'   => $this->getStoreItemUrl($item),
                'label'  => __('View'),
                'target' => '_blank'

            ];
        }

        return $dataSource;
    }


    /**
     * @param array $item
     * @param string $type
     * @return string
     */
    protected function getStoreItemUrl($item, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        $storeId = $this->getStoreId($item);
        /** @var \Magento\Store\Model\Store $store */
        $store    = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();

        $url = rtrim($store->getBaseUrl($type, $isSecure), '/') . '/' . ltrim($item['url_path'], '/');

        if (!$store->isUseStoreInUrl()) {
            $url .= '?___store=' . $store->getCode();
        }

        return $url;
    }

    /**
     * @param array $item
     * @return int
     */
    protected function getStoreId($item)
    {
        return !empty($item['store_id_orig']) ? $item['store_id_orig'] : (int)$item['store_id'];
    }
}
