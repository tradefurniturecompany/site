<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\ConverterCategoryFactory;

abstract class Category extends \MageWorx\SeoXTemplates\Model\DataProvider
{
    protected $converterCategoryFactory;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    public function __construct(
        ResourceConnection $resource,
        ConverterCategoryFactory $converterCategoryFactory
    ) {
        parent::__construct($resource);
        $this->converterCategoryFactory = $converterCategoryFactory;
    }
}
