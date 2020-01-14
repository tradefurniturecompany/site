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

namespace Customweb\RealexCw\Model;

class Configuration
{
	/**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $__moduleReader;

    /**
     * Xml Parser
     *
     * @var \Magento\Framework\Xml\Parser
     */
    protected $_xmlParser;

    /**
     * @var array
     */
    private $defaultValues = [];

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store = null;

    /**
     * @var array
     */
    private $_reservedIds = [
    	'instance'
    ];

	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Framework\Module\Dir\Reader $moduleReader,
			\Magento\Framework\Xml\Parser $xmlParser,
			\Magento\Sales\Model\Order\Config $orderConfig
	) {
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_moduleReader = $moduleReader;
		$this->_xmlParser = $xmlParser;

		$this->initDefaultValues();
		$this->initTypes();

		$this->store = $this->_storeManager->getStore();
	}

	/**
	 * @return \Magento\Store\Model\Store
	 */
	public function getStore()
	{
		return $this->store;
	}

	/**
	 * @param \Magento\Store\Model\Store|int $store
	 */
	public function setStore($store)
	{
		if ($store instanceof \Magento\Store\Model\Store) {
			$this->store = $store;
		} else {
			$this->store = $this->_storeManager->getStore($store);
		}
	}

	public function getConfigurationValue($path, $key)
	{
		$key = $this->getCleanId($key);

		$value = $this->_scopeConfig->getValue($path . '/' . $key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->store->getId());

		switch ($this->getType($key)) {
			case 'multiselect':
				return empty($value) ? [] : explode(',', $value);
			case 'file':
				if (empty($value)) {
					$value = $this->getDefaultValue($key);
				}
				return $this->getAssetResolver($key)->resolveAssetStream($value);
			default:
				return $value;
		}
	}

	public function existsConfiguration($path, $key)
	{
		$key = $this->getCleanId($key);

		return $this->_scopeConfig->getValue($path . '/' . $key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->store->getId()) !== null;
	}

	private function initDefaultValues()
	{
		$modulesDirectory = $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Customweb_RealexCw');
		$this->_xmlParser->load($modulesDirectory . '/config.xml');
		$xmlArray = $this->_xmlParser->xmlToArray();
		foreach ($xmlArray['config']['_value']['default']['realexcw']['general'] as $key => $value) {
			if (is_array($value)) {
				$value = $value['_value'];
			}
			$this->defaultValues[$key] = $value;
		}

		foreach ($xmlArray['config']['_value']['default']['payment'] as $paymentMethod => $values) {
			foreach ($values as $key => $value) {
				if (is_array($value)) {
					$value = $value['_value'];
				}
				$this->defaultValues[$paymentMethod . '/' . $key] = $value;
			}
		}
	}

	private function getDefaultValue($key)
	{
		if (isset($this->defaultValues[$key])) {
			return $this->defaultValues[$key];
		} else {
			return null;
		}
	}

	private function initTypes()
	{
		$modulesDirectory = $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Customweb_RealexCw');
		$this->_xmlParser->load($modulesDirectory . '/adminhtml/system.xml');
		$xmlArray = $this->_xmlParser->xmlToArray();
		foreach ($xmlArray['config']['_value']['system']['section'] as $section) {
			if (isset($section['_value']['group']['_value'])) {
				$section['_value']['group'] = [$section['_value']['group']];
			}
			foreach ($section['_value']['group'] as $group) {
				if (isset($group['_value']['field']['_value'])) {
					$group['_value']['field'] = [$group['_value']['field']];
				}
				foreach ($group['_value']['field'] as $field) {
					if (!isset($field['_attribute']['type'])) {
						continue;
					}
					$key = $field['_attribute']['id'];
					if ($section['_attribute']['id'] == 'payment') {
						$key = $group['_attribute']['id'] . '/' . $key;
					}
					$this->types[$key] = $field['_attribute']['type'];
				}
			}
		}
	}

	private function getType($key)
	{
		if (isset($this->types[$key])) {
			return $this->types[$key];
		} else {
			return 'text';
		}
	}

	/**
	 *
	 * @param string $id
	 * @return string
	 */
	private function getCleanId($id) {
		$cleanId = preg_replace('/[^a-zA-Z0-9_\/]/', '_', $id);
		if (in_array($cleanId, $this->_reservedIds)) {
			return $cleanId . '_value';
		} else {
			return $cleanId;
		}
	}

	/**
	 * @param string $key
	 * @return \Customweb_Asset_IResolver
	 */
	private function getAssetResolver($key)
	{
		$mediaDirectory = $this->_storeManager->getStore()->getBaseMediaDir() . '/customweb/realexcw';
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '/customweb/realexcw';
		return new \Customweb_Asset_Resolver_Composite([
			new \Customweb_Asset_Resolver_Simple(
					$mediaDirectory . '/config/' . $key,
					$mediaUrl . '/config/' . $key
			),
			new \Customweb_Asset_Resolver_Simple(
					$mediaDirectory . '/assets/' . $key,
					$mediaUrl . '/assets/' . $key
			)
		]);
	}

}