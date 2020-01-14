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

class UpdateLink
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(LinkInterface::TABLE_NAME);

        $setup->getConnection()->modifyColumn(
            $tableName,
            LinkInterface::LINK,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null
            ]
        );
    }
}
