<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

use Magento\Framework\App\ResourceConnection;

abstract class DataProvider implements DataProviderInterface
{
    protected $converterProductFactory;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Retrieve data
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return array
     */
    abstract public function getData($collection, $template, $customStoreId = null);

    /**
     *
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @param array $additionalData
     * @return mixed|void
     */
    public function addFiltersToEntityCollection($template, $collection)
    {
        return $collection;
    }

    /**
     * You can load collection and add specific data to items here
     *
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection Non-loaded collection
     * @return mixed|void
     */
    public function onLoadEntityCollection($template, $collection)
    {
        return $collection;
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
