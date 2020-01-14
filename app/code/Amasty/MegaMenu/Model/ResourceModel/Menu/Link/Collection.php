<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Link;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(LinkInterface::ENTITY_ID);
        $this->_init(
            \Amasty\MegaMenu\Model\Menu\Link::class,
            \Amasty\MegaMenu\Model\ResourceModel\Menu\Link::class
        );
    }
}
