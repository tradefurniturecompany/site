<?php
namespace Magesales\Shippingtable\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		$table = $installer->getConnection()->newTable($installer->getTable('shippingmethod'))
		->addColumn(
			'method_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			8,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Method Id Primary key'
		)
		->addColumn(
			'is_active',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Is Active'
        )
		->addColumn(
			'pos',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Position'
        )
		->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
		)
		->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Comment'
		)
		->addColumn(
            'stores',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Website Stores'
		)
		->addColumn(
            'cust_groups',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Groups'
		)
		->addColumn(
			'select_rate',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			2,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Select Rate'
        );
		$installer->getConnection()->createTable($table);
		
		$table1 = $installer->getConnection()->newTable($installer->getTable('shippingrate'))
		->addColumn(
			'rate_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Rate Id Primary key'
		)
		->addColumn(
			'method_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			8,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Is Active'
        )
		->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            4,
            ['nullable' => false],
            'Country'
		)
		->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'State'
		)
		->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            12,
            ['nullable' => false],
            'City'
		)
		->addColumn(
            'zip_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Zip From'
		)
		->addColumn(
            'zip_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Zip To'
		)
		->addColumn(
			'price_from',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Price From'
		)
		->addColumn(
			'price_to',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Price To'
		)
		->addColumn(
			'weight_from',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Weight From'
		)
		->addColumn(
			'weight_to',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Weight To'
		)
		->addColumn(
			'qty_from',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Quantity From'
		)
		->addColumn(
			'qty_to',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Quantity To'
		)
		->addColumn(
			'shipping_type',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Shipping Type'
		)
		->addColumn(
			'cost_base',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Cost Base'
		)
		->addColumn(
			'cost_percent',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'5,2',
			['default' => '0'],
			'Cost Percent'
		)
		->addColumn(
			'cost_product',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['default' => '0'],
			'Cost Product'
		)
		->addColumn(
			'cost_weight',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,2',
			['default' => '0'],
			'Cost Weight'
		)
		->addColumn(
			'time_delivery',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
			'Time Delivery'
		)
		->addIndex(
			$installer->getIdxName(
				'shippingrate',
				['method_id', 'country', 'state','city', 'zip_from', 'zip_to','price_from', 'price_to', 'weight_from','weight_to', 'qty_from', 'qty_to', 'shipping_type'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
			),
			['method_id', 'country', 'state','city', 'zip_from', 'zip_to','price_from', 'price_to', 'weight_from','weight_to', 'qty_from', 'qty_to', 'shipping_type'],
			['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
		);
		
		$installer->getConnection()->createTable($table1);
	}
}
