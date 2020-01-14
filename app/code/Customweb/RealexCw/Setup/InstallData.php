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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	/**
	 * Sales setup factory
	 *
	 * @var \Magento\SalesSequence\Model\EntityPool
	 */
	private $_entityPool;

	/**
	 * @var \Magento\SalesSequence\Model\Builder
	 */
	private $_sequenceBuilder;

	/**
	 * @var \Magento\SalesSequence\Model\Config
	 */
	private $_sequenceConfig;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	private $_storeManager;

	/**
	 * @param \Magento\SalesSequence\Model\EntityPool $entityPool
	 * @param \Magento\SalesSequence\Model\Builder $sequenceBuilder
	 * @param \Magento\SalesSequence\Model\Config $sequenceConfig
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
		\Magento\SalesSequence\Model\EntityPool $entityPool,
		\Magento\SalesSequence\Model\Builder $sequenceBuilder,
		\Magento\SalesSequence\Model\Config $sequenceConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->_entityPool = $entityPool;
		$this->_sequenceBuilder = $sequenceBuilder;
		$this->_sequenceConfig = $sequenceConfig;
		$this->_storeManager = $storeManager;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		foreach (array_keys($this->_storeManager->getStores(true)) as $storeId) {
			$this->_sequenceBuilder->setPrefix($this->_sequenceConfig->get('prefix'))
				->setSuffix($this->_sequenceConfig->get('suffix'))
				->setStartValue($this->_sequenceConfig->get('startValue'))
				->setStoreId($storeId)
				->setStep($this->_sequenceConfig->get('step'))
				->setWarningValue($this->_sequenceConfig->get('warningValue'))
				->setMaxValue($this->_sequenceConfig->get('maxValue'))
				->setEntityType('rexcw_trx')->create();
		}
	}
}