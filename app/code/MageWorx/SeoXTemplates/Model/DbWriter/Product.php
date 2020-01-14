<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DbWriter;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;

abstract class Product extends \MageWorx\SeoXTemplates\Model\DbWriter
{
    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\DataProviderProductFactory
     */
    protected $dataProviderProductFactory;

    /**
     *
     * @param ResourceConnection $resource
     * @param DataProviderProductFactory $dataProviderProductFactory
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderProductFactory $dataProviderProductFactory
    ) {
        parent::__construct($resource);
        $this->dataProviderProductFactory = $dataProviderProductFactory;
    }
}
