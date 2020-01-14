<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\CsvWriter;

use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;

abstract class Product extends \MageWorx\SeoXTemplates\Model\CsvWriter
{
    /**
     * Write to CSV file converted string from template code and retrive file params
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param string|null $filenameParam
     * @param int|null $nestedStoreId
     * @return array
     */
    abstract public function write($collection, $template, $filenameParam = null, $nestedStoreId = null);

    /**
     *
     * @var DataProviderCategoryFactory
     */
    protected $dataProviderProductFactory;

    /**
     *
     * @param Filesystem $fileSystem
     * @param DataProviderProductFactory $dataProviderProductFactory
     */
    public function __construct(
        Filesystem $fileSystem,
        DataProviderProductFactory $dataProviderProductFactory
    ) {
        parent::__construct($fileSystem);
        $this->dataProviderProductFactory = $dataProviderProductFactory;
    }
}
