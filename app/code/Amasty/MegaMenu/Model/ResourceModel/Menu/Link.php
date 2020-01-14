<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;

class Link extends AbstractDb
{
    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LinkInterface::TABLE_NAME, LinkInterface::ENTITY_ID);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $itemContentTable = $this->getTable(ItemInterface::TABLE_NAME);
        $select->joinInner(
            $itemContentTable,
            "{$itemContentTable}.entity_id = {$this->getMainTable()}.entity_id AND store_id = 0 AND {$itemContentTable}.type = 'custom'"
        );

        return $select;
    }
}
