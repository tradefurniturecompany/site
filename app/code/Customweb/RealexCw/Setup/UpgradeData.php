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

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {

	/**
	 *
	 * @var EncryptorInterface
	 */
	private $_encryptor;

	/**
	 *
	 * @param EncryptorInterface $encryptor
	 */
	public function __construct(EncryptorInterface $encryptor){
		$this->_encryptor = $encryptor;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
		$installer = $setup;
		$installer->startSetup();

		if (version_compare($context->getVersion(), '1.0.3') < 0) {
			/**
			 * Encrypt password setting values.
			 */
			$globalSettings = \array_map(function ($path){
				return "'realexcw/general/{$path}'";
			}, [ 
			]);
			if (!empty($globalSettings)) {
				$globalRows = $installer->getConnection()->fetchAll(
						"select * from {$installer->getTable('core_config_data')} where
						path in (" . \implode(',', $globalSettings) . ")");
			}
			else {
				$globalRows = array();
			}

			$paymentMethodSettings = \array_map(function ($path){
				return "'payment/{$path}'";
			}, [ 
			]);
			if (!empty($paymentMethodSettings)) {
				$paymentMethodRows = $installer->getConnection()->fetchAll(
						"select * from {$installer->getTable('core_config_data')} where
						path in (" . \implode(',', $paymentMethodSettings) . ")");
			}
			else {
				$paymentMethodRows = array();
			}

			$rows = \array_merge($globalRows, $paymentMethodRows);

			foreach ($rows as $row) {
				if (!empty($row['value'])) {
					$row['value'] = $this->_encryptor->encrypt($row['value']);
					$installer->getConnection()->update($installer->getTable('core_config_data'), $row, 'config_id=' . $row['config_id']);
				}
			}
		}

		$installer->endSetup();
	}

}