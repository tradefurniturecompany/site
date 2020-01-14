<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\CsvWriter\Product;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;

class Eav extends \MageWorx\SeoXTemplates\Model\CsvWriter\Product
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_collection;

    /**
     * Eav constructor.
     *
     * @param StoreManagerInterface $_storeManager
     * @param Filesystem $fileSystem
     * @param DataProviderProductFactory $dataProviderProductFactory
     */
    public function __construct(
        StoreManagerInterface $_storeManager,
        Filesystem $fileSystem,
        DataProviderProductFactory $dataProviderProductFactory
    ) {
        $this->_storeManager = $_storeManager;
        parent::__construct($fileSystem, $dataProviderProductFactory);
    }

    /**
     * Write to CSV file converted string from template code and retrive file params
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param string|null $filenameParam
     * @param int|null $nestedStoreId
     * @return array
     */
    public function write($collection, $template, $filenameParam = null, $nestedStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection = $collection;
        $dataProvider      = $this->dataProviderProductFactory->create($template->getTypeId());
        $data              = $dataProvider->getData($collection, $template, $nestedStoreId);

        $filename = $filenameParam ? $filenameParam : 'export/'. md5(microtime()) . '.csv';

        $stream = $this->directory->openFile($filename, 'a+');
        if (!$filenameParam) {
            $stream->writeCsv($this->_getHeaderData());
        }

        $connect = $dataProvider->getCollectionIds();
        $stream->lock();

        foreach ($data as $attributeHash => $attributeData) {
            foreach ($attributeData as $multipleData) {
                list($attributeId, $attributeCode) = explode('#', $attributeHash);

                foreach ($multipleData as $entityId => $data) {
                    $product = $this->_collection->getItemById($connect[$entityId]);
                    if (!$product) {
                        continue;
                    }

                    if (empty($data['value'])) {
                        continue;
                    }

                    if ($data['store_id'] == '0') {
                        $storeName = __('Single-Store Mode');
                    } else {
                        $storeName = $this->_storeManager->getStore($nestedStoreId)->getName();
                    }

                    $write = [
                        'attribute_id'     => $attributeId,
                        'attribute_code'   => $attributeCode,
                        'product_id'       => $connect[$entityId],
                        'product_name'     => $this->_collection->getItemById($connect[$entityId])->getName(),
                        'store_id'         => $data['store_id'],
                        'store_name'       => $storeName,
                        'current_value'    => !empty($data['old_value']) ? $data['old_value'] : '',
                        'value'            => $data['value']
                    ];

                    $stream->writeCsv($write);
                }
            }
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $filename,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Retrieve header for report
     * @return array
     */
    protected function _getHeaderData()
    {
        return [
            __('Attribute ID'),
            __('Attribute Code'),
            __('Product ID'),
            __('Product Name'),
            __('Store ID'),
            __('Store Name'),
            __('Current Store Value'),
            __('New Store Value')
        ];
    }
}
