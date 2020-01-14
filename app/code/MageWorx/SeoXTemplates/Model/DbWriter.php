<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

use Magento\Framework\App\ResourceConnection;

abstract class DbWriter implements DbWriterInterface
{
    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Write to database converted string from template code
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return array|boolean
     */
    abstract public function write($collection, $template, $customStoreId = null);

    /**
     *
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_connection = $this->_resource->getConnection();
    }

    /**
     * Retrieve write connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }
        return $this->_connection;
    }
}
