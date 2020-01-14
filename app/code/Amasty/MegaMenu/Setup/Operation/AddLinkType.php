<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup\Operation;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\Setup\SchemaSetupInterface;

class AddLinkType
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(LinkInterface::TABLE_NAME);

        $setup->getConnection()->addColumn(
            $tableName,
            LinkInterface::TYPE,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Link type',
            ]
        );
        $setup->getConnection()->addColumn(
            $tableName,
            LinkInterface::PAGE_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Page id',
            ]
        );
    }
}
