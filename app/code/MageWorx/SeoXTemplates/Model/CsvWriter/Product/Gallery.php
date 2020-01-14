<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\CsvWriter\Product;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;

class Gallery extends \MageWorx\SeoXTemplates\Model\CsvWriter\Product
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
     * Gallery constructor.
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

        $filename = $filenameParam ? $filenameParam : 'export/' . md5(microtime()) . '.csv';

        $stream = $this->directory->openFile($filename, 'a+');
        if (!$filenameParam) {
            $stream->writeCsv($this->_getHeaderData());
        }

        $stream->lock();

        foreach ($data as $attributeCode => $attributeData) {

            foreach ($attributeData as $entityId => $multipleData) {

                foreach ($multipleData as $valueId => $data) {

                    $product = $this->_collection->getItemById($entityId);
                    if (!$product) {
                        continue;
                    }

                    if (empty($data['label'])) {
                        continue;
                    }

                    if ($data['store_id'] == '0') {
                        $storeName = __('Single-Store Mode');
                    } else {
                        $storeName = $this->_storeManager->getStore($nestedStoreId)->getName();
                    }

                    $write = [
                        'attribute_code' => $attributeCode,
                        'product_id'     => $entityId,
                        'product_name'   => $this->_collection->getItemById($entityId)->getName(),
                        'store_id'       => $data['store_id'],
                        'store_name'     => $storeName,
                        'file'           => $data['file'],
                        'position'       => $data['position'],
                        'current_value'  => !empty($data['old_label']) ? $data['old_label'] : '',
                        'value'          => $data['label']
                    ];

                    $stream->writeCsv($write);
                }
            }
        }

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $filename,
            'rm'    => true  // can delete file after use
        ];
    }

    /**
     * Retrieve header for report
     *
     * @return array
     */
    protected function _getHeaderData()
    {
        return [
            __('Attribute Code'),
            __('Product ID'),
            __('Product Name'),
            __('Store ID'),
            __('Store Name'),
            __('File'),
            __('Position'),
            __('Current Store Value'),
            __('New Store Value')
        ];
    }
}
