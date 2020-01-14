<?php

/**
 *  * You are allowed to use this API in your web application.
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
 * @category Customweb
 * @package Customweb_RealexCw
 *
 */
namespace Customweb\RealexCw\Setup;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 *
	 * @var StoreManagerInterface
	 */
	private $_storeManager;

	/**
	 *
	 * @var AppResource
	 */
	private $_appResource;

	/**
	 *
	 * @param StoreManagerInterface $storeManager
	 * @param AppResource $appResource
	 */
	public function __construct(StoreManagerInterface $storeManager, AppResource $appResource){
		$this->_storeManager = $storeManager;
		$this->_appResource = $appResource;
	}

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$installer = $setup;
		$installer->startSetup();

		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			/**
			 * Add column 'send_email' to 'customweb_realexcw_transaction'
			 */
			if ($installer->getConnection()->isTableExists($installer->getTable('customweb_realexcw_transaction'))) {
				$installer->getConnection()->addColumn($installer->getTable('customweb_realexcw_transaction'), 'send_email',
						[
							'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
							'default' => true,
							'comment' => 'Send Email'
						]);
			}
		}

		if (version_compare($context->getVersion(), '1.0.4') < 0) {
			/**
			 * Rename sequence tables
			 */
			$rows = $installer->getConnection()->fetchAll(
					"select * from {$installer->getTable('sales_sequence_meta')} where entity_type = 'realexcw_transaction'");
			foreach ($rows as $row) {
				$row['entity_type'] = 'realexcw_trx';
				$row['sequence_table'] = $this->getSequenceName('realexcw_trx', $row['store_id']);
				$installer->getConnection()->update($installer->getTable('sales_sequence_meta'), $row, 'meta_id=' . $row['meta_id']);
			}

			foreach (array_keys($this->_storeManager->getStores(true)) as $storeId) {
				$oldTableName = $this->getSequenceName('realexcw_transaction', $storeId);
				if ($installer->getConnection()->isTableExists($oldTableName)) {
					$newTableName = $this->getSequenceName('realexcw_trx', $storeId);
					$installer->getConnection()->renameTable($oldTableName, $newTableName);
				}
			}
		}

		if (version_compare($context->getVersion(), '1.0.5') < 0) {
			/**
			 * Shorten names of sequence tables
			 */
			$rows = $installer->getConnection()->fetchAll(
					"select * from {$installer->getTable('sales_sequence_meta')} where entity_type = 'realexcw_trx'");
			foreach ($rows as $row) {
				$row['entity_type'] = 'rexcw_trx';
				$row['sequence_table'] = $this->getSequenceName('rexcw_trx', $row['store_id']);
				$installer->getConnection()->update($installer->getTable('sales_sequence_meta'), $row, 'meta_id=' . $row['meta_id']);
			}

			foreach (array_keys($this->_storeManager->getStores(true)) as $storeId) {
				$oldTableName = $this->getSequenceName('realexcw_trx', $storeId);
				if ($installer->getConnection()->isTableExists($oldTableName)) {
					$newTableName = $this->getSequenceName('rexcw_trx', $storeId);
					$installer->getConnection()->renameTable($oldTableName, $newTableName);
				}
			}
		}

		$installer->endSetup();
	}

	private function getSequenceName($entityType, $storeId){
		return $this->_appResource->getTableName(\sprintf('sequence_%s_%s', $entityType, $storeId));
	}

}