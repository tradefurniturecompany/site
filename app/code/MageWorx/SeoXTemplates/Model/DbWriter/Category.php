<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DbWriter;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderCategoryFactory;

abstract class Category extends \MageWorx\SeoXTemplates\Model\DbWriter
{
    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\DataProviderCategoryFactory
     */
    protected $dataProviderCategoryFactory;

    /**
     *
     * @param ResourceConnection $resource
     * @param DataProviderCategoryFactory $dataProviderCategoryFactory
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderCategoryFactory $dataProviderCategoryFactory
    ) {
        parent::__construct($resource);
        $this->dataProviderCategoryFactory = $dataProviderCategoryFactory;
    }
}
