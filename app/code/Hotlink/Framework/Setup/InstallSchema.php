<?php
namespace Hotlink\Framework\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install( \Magento\Framework\Setup\SchemaSetupInterface $setup,
                             \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable( 'hotlink_framework_report_log' ))
            ->addColumn(
                'record_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Unique PK')
            ->addColumn(
                'user',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [ 'nullable' => false ],
                'Initiating user')
            ->addColumn(
                'event',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [ 'nullable' => false ],
                'Initiating event ')
            ->addColumn(
                'trigger',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [ 'nullable' => false ],
                'Initiating trigger' )
            ->addColumn(
                'interaction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [ 'nullable' => false ],
                'Name of interaction executed' )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'As defined in Flint_Interaction_Model_Interaction_Abstract' )
            ->addColumn(
                'started',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => false, 'default' => '0000-00-00 00:00:00' ],
                'When task started' )
            ->addColumn(
                'ended',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'When task ended' )
            ->addColumn(
                'success',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Counter for success' )
            ->addColumn(
                'fail',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Counter for failure' )
            ->addColumn(
                'reference',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                [ 'nullable'  => true ],
                'Used for storing searchable information')
            ->addColumn(
                'context',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable'  => true ],
                'Context under which the interaction was triggered');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}