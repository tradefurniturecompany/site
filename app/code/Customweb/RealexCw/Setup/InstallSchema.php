<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 *
 */

namespace Customweb\RealexCw\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Create table 'customweb_realexcw_transaction'
		 */
		if (!$installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_transaction'))) {
			$table = $installer->getConnection()->newTable(
					$installer->getTable('customweb_realexcw_transaction')
			)->addColumn(
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'Entity ID'
			)->addColumn(
					'transaction_external_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => true],
					'Transaction External Id'
			)->addColumn(
					'order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true],
					'Order Id'
			)->addColumn(
					'order_payment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'nullable' => true],
					'Order Payment Id'
			)->addColumn(
					'alias_for_display',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => true],
					'Alias For Display'
			)->addColumn(
					'alias_active',
					\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					null,
					['default' => false],
					'Alias Active'
			)->addColumn(
					'payment_method',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Payment Method'
			)->addColumn(
					'transaction_object',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					['nullable' => true],
					'Transaction Object'
			)->addColumn(
					'authorization_type',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Authorization Type'
			)->addColumn(
					'customer_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true],
					'Customer Id'
			)->addColumn(
		            'increment_id',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		            50,
		            [],
		            'Increment Id'
			)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
			)->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
					'Updated At'
			)->addColumn(
					'payment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => true],
					'Payment Id'
			)->addColumn(
					'execute_update_on',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => true],
					'Execute Update On'
			)->addColumn(
					'authorization_amount',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'20,5',
					[],
					'Authorization Amount'
			)->addColumn(
					'authorization_status',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Authorization Status'
			)->addColumn(
					'paid',
					\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					null,
					['default' => false],
					'Paid'
			)->addColumn(
					'live_transaction',
					\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					null,
					['default' => false],
					'Live Transaction'
			)->addColumn(
					'currency',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					3,
					[],
					'Currency'
			)->addColumn(
					'store_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
					null,
					['unsigned' => true],
					'Store Id'
			)->addColumn(
					'version_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'default' => 1],
					'Version Number'
			)->addIndex(
		            $installer->getIdxName(
		                'customweb_realexcw_transaction',
		                ['increment_id', 'store_id'],
		                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
		            ),
		            ['increment_id', 'store_id'],
		            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			)->addIndex(
					$installer->getIdxName(
							'customweb_realexcw_transaction',
							['transaction_external_id', 'order_id', 'payment_id']
					),
					['transaction_external_id', 'order_id', 'payment_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction', ['order_id']),
					['order_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction', ['order_payment_id']),
					['order_payment_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction', ['customer_id']),
					['customer_id']
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_transaction', 'order_id', 'sales_order', 'entity_id'),
					'order_id',
					$installer->getTable('sales_order'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_transaction', 'order_payment_id', 'sales_order_payment', 'entity_id'),
					'order_payment_id',
					$installer->getTable('sales_order_payment'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_transaction', 'customer_id', 'customer_entity', 'entity_id'),
					'customer_id',
					$installer->getTable('customer_entity'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_transaction', 'store_id', 'store', 'store_id'),
					'store_id',
					$installer->getTable('store'),
					'store_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->setComment(
					'Customweb RealexCw Transaction Table'
			);
			$installer->getConnection()->createTable($table);
		}

		/**
		 * Create table 'customweb_realexcw_transaction_grid'
		 */
		if (!$installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_transaction_grid'))) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('customweb_realexcw_transaction_grid')
			)->addColumn(
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'nullable' => false, 'primary' => true],
					'Entity Id'
			)->addColumn(
					'store_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
					null,
					['unsigned' => true],
					'Store Id'
			)->addColumn(
					'order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true],
					'Order Id'
			)->addColumn(
					'order_increment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Order Increment Id'
			)->addColumn(
					'customer_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Customer Name'
			)->addColumn(
					'payment_method',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Payment Method'
			)->addColumn(
					'authorization_type',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Authorization Type'
			)->addColumn(
		            'increment_id',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		            50,
		            [],
		            'Increment Id'
			)->addColumn(
		            'created_at',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		            null,
		            [],
		            'Created At'
	        )->addColumn(
		            'updated_at',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		            null,
		            [],
		            'Updated At'
	        )->addColumn(
					'payment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => true],
					'Payment Id'
	        )->addColumn(
					'authorization_amount',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'20,5',
					[],
					'Authorization Amount'
			)->addColumn(
					'authorization_status',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Authorization Status'
			)->addColumn(
					'currency',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					3,
					[],
					'Currency'
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['store_id']),
					['store_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['order_id']),
					['order_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['order_increment_id']),
					['order_increment_id']
			)->addIndex(
					$installer->getIdxName(
							'customweb_realexcw_transaction_grid',
							['increment_id', 'store_id'],
							\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
					),
					['increment_id', 'store_id'],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['increment_id']),
					['increment_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['created_at']),
					['created_at']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['updated_at']),
					['updated_at']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['payment_id']),
					['payment_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_transaction_grid', ['authorization_status']),
					['authorization_status']
			)->addIndex(
					$installer->getIdxName(
							'customweb_realexcw_transaction_grid',
							[
								'order_increment_id',
								'customer_name',
								'authorization_type',
								'payment_id'
							],
							\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					[
						'order_increment_id',
						'customer_name',
						'authorization_type',
						'payment_id'
					],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
			)->setComment(
					'Customweb RealexCw Transaction Grid'
			);
			$installer->getConnection()->createTable($table);
		}

		/**
		 * Create table 'customweb_realexcw_customer_context'
		 */
		if (!$installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_customer_context'))) {
			$table = $installer->getConnection()->newTable(
					$installer->getTable('customweb_realexcw_customer_context')
			)->addColumn(
					'context_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'Context Id'
			)->addColumn(
					'customer_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true],
					'Customer Id'
			)->addColumn(
					'values',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'64k',
					['nullable' => true],
					'Values'
			)->addColumn(
					'version_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'default' => 1],
					'Version Number'
			)->addIndex(
					$installer->getIdxName(
							'customweb_realexcw_customer_context',
							['customer_id'],
							\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
					),
					['customer_id'],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_customer_context', 'customer_id', 'customer_entity', 'entity_id'),
					'customer_id',
					$installer->getTable('customer_entity'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->setComment(
					'Customweb RealexCw Customer Context Table'
			);
			$installer->getConnection()->createTable($table);
		}

		/**
		 * Create table 'customweb_realexcw_storage'
		 */
		if (!$installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_storage'))) {
			$table = $installer->getConnection()->newTable(
					$installer->getTable('customweb_realexcw_storage')
			)->addColumn(
					'key_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'Key Id'
			)->addColumn(
					'key_space',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					165,
					[],
					'Key Space'
			)->addColumn(
					'key_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					165,
					[],
					'Key Name'
			)->addColumn(
					'key_value',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					['nullable' => true],
					'Key Text'
			)->addIndex(
					$installer->getIdxName(
						'customweb_realexcw_storage',
						['key_space', 'key_name'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
					),
					['key_space', 'key_name'],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			)->setComment(
					'Customweb RealexCw Storage Table'
			);
			$installer->getConnection()->createTable($table);
		}

		/**
		 * Create table 'customweb_realexcw_external_checkout_context'
		 */
		if (!$installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_external_checkout_context'))) {
			$table = $installer->getConnection()->newTable(
					$installer->getTable('customweb_realexcw_external_checkout_context')
			)->addColumn(
					'context_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
					'Context Id'
			)->addColumn(
					'state',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'State'
			)->addColumn(
					'failed_error_message',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Failed Error Message'
			)->addColumn(
					'cart_url',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					512,
					[],
					'Cart Url'
			)->addColumn(
					'default_checkout_url',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					512,
					[],
					'Default Checkout Url'
			)->addColumn(
					'invoice_items',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					[],
					'Invoice Items'
			)->addColumn(
					'order_amount_in_decimals',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'20,5',
					[],
					'Order Amount In Decimals'
			)->addColumn(
					'currency_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					3,
					[],
					'Currency Code'
			)->addColumn(
					'language_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					2,
					[],
					'Language Code'
			)->addColumn(
					'customer_email_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Customer Email Address'
			)->addColumn(
					'customer_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'nullable' => true],
					'Customer Id'
			)->addColumn(
					'transaction_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'nullable' => true],
					'Transaction Id'
			)->addColumn(
					'shipping_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					['nullable' => true],
					'Shipping Address'
			)->addColumn(
					'billing_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					['nullable' => true],
					'Billing Address'
			)->addColumn(
					'shipping_method_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Shipping Method Name'
			)->addColumn(
					'payment_method',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Payment Method'
			)->addColumn(
					'provider_data',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
					['nullable' => true],
					'Provider Data'
			)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
			)->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
					'Updated At'
			)->addColumn(
					'security_token',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Security Token'
			)->addColumn(
					'security_token_expiry_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					[],
					'Security Token Expiry Date'
			)->addColumn(
					'authentication_success_url',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					512,
					['nullable' => true],
					'Authentication Success Url'
			)->addColumn(
					'authentication_email_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Authentication Email Address'
			)->addColumn(
					'quote_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true],
					'Quote Id'
			)->addColumn(
					'register_method',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => true],
					'Register Method'
			)->addColumn(
					'store_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
					null,
					['unsigned' => true],
					'Store Id'
			)->addColumn(
					'version_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					['unsigned' => true, 'default' => 1],
					'Version Number'
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_external_checkout_context', ['customer_id']),
					['customer_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_external_checkout_context', ['transaction_id']),
					['transaction_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_external_checkout_context', ['quote_id']),
					['quote_id']
			)->addIndex(
					$installer->getIdxName('customweb_realexcw_external_checkout_context', ['store_id']),
					['store_id']
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_external_checkout_context', 'customer_id', 'customer_entity', 'entity_id'),
					'customer_id',
					$installer->getTable('customer_entity'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_external_checkout_context', 'transaction_id', 'customweb_realexcw_transaction', 'entity_id'),
					'transaction_id',
					$installer->getTable('customweb_realexcw_transaction'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_external_checkout_context', 'quote_id', 'quote', 'entity_id'),
					'quote_id',
					$installer->getTable('quote'),
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->addForeignKey(
					$installer->getFkName('customweb_realexcw_external_checkout_context', 'store_id', 'store', 'store_id'),
					'store_id',
					$installer->getTable('store'),
					'store_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
			)->setComment(
					'Customweb RealexCw Customer Context Table'
			);
			$installer->getConnection()->createTable($table);
		}

		$installer->endSetup();
	}
}
