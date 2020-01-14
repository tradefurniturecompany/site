<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\CsvWriter;

use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderCategoryFilterFactory;

class CategoryFilter extends \MageWorx\SeoXTemplates\Model\CsvWriter
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DataProviderCategoryFilterFactory
     */
    protected $dataProviderCategoryFilterFactory;

    /**
     * CategoryFilter constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param Filesystem $fileSystem
     * @param DataProviderCategoryFilterFactory $dataProviderCategoryFilterFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        Filesystem $fileSystem,
        DataProviderCategoryFilterFactory $dataProviderCategoryFilterFactory
    ) {
        $this->_storeManager = $_storeManager;
        $this->dataProviderCategoryFilterFactory = $dataProviderCategoryFilterFactory;
        parent::__construct($fileSystem);
    }

    /**
     * Write to CSV file converted string from template code and retrive file params
     *
     * @param \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param string|null $filenameParam
     * @param int|null $nestedStoreId
     * @return array
     */
    public function write($collection, $template, $filenameParam = null, $nestedStoreId = null)
    {
        if (!$collection->count()) {
            return false;
        }

        $dataProvider = $this->dataProviderCategoryFilterFactory->create($template->getTypeId());
        $data = $dataProvider->getData($collection, $template, $nestedStoreId);

        $filename = $filenameParam ? $filenameParam : 'export/'. md5(microtime()) . '.csv';

        $stream = $this->directory->openFile($filename, 'a+');
        if (!$filenameParam) {
            $stream->writeCsv($this->_getHeaderData());
        }

        $stream->lock();

        foreach ($data as $categoryFilterId => $categoryFilterData) {
            if (empty($categoryFilterData['value'])) {
                continue;
            }

            $write = $categoryFilterData;
            $stream->writeCsv($write);
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
     *
     * @return array
     */
    protected function _getHeaderData()
    {
        return [
            __('SEO Filter ID'),
            __('Attribute ID'),
            __('Attribute Code'),
            __('Attribute Value ID'),
            __('Attribute Value Label'),
            __('Category ID'),
            __('Category Name'),
            __('Store ID'),
            __('Store Name'),
            __('Target Property'),
            __('Current Store Value'),
            __('New Store Value')
        ];
    }
}
