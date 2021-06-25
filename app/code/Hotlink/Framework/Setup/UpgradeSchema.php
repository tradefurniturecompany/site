<?php
namespace Hotlink\Framework\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context )
    {
        $setup->startSetup();

        if ( version_compare( $context->getVersion(), '2.1.0', '<' ) )
            {
                $this->updateReportLogTable( $setup, $context );
            }

        $setup->endSetup();
    }

    protected function updateReportLogTable( $setup, $context )
    {
        $tableName = 'hotlink_framework_report_log';
        $setup
            ->getConnection()
            ->modifyColumn( $setup->getTable( $tableName ),
                            'context',
                            [
                                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                'length'   => 65535,
                                'nullable' => true,
                                'comment'  => 'Context under which the interaction was triggered'
                            ],
                            false );
    }

    /*
    
      reference:

      vi lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php

      public function addColumn($tableName, $columnName, $definition, $schemaName = null)
      public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false, $schemaName = null)
      public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
      public function dropColumn($tableName, $columnName, $schemaName = null)

    */

}
