<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.0') < 0) {

            $cmsPageTable = $setup->getTable('cms_page');

            if ($setup->getConnection()->isTableExists($cmsPageTable) == true) {
                $columns = [
                    'use_in_crosslinking' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 1,
                        'comment' => 'MageWorx flag for using in CrossLinking',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($cmsPageTable, $name, $definition);
                }
            }

            $crosslinkTable = $setup->getTable('mageworx_seocrosslinks_crosslink');

            if ($setup->getConnection()->isTableExists($crosslinkTable) == true) {
                $columns = [
                    'nofollow_rel' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Nofollow rel for crosslink',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($crosslinkTable, $name, $definition);
                }
            }

        }
        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $crosslinkTable = $setup->getTable('mageworx_seocrosslinks_crosslink');

            if ($setup->getConnection()->isTableExists($crosslinkTable)) {
                $columns = [
                    'ref_landingpage_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Reference by Landing Page',
                    ],
                    'in_landingpage' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default' => 1,
                        'comment' => 'Use in  Landing Page',
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($crosslinkTable, $name, $definition);
                }
            }
        }

        $setup->endSetup();
    }
}
