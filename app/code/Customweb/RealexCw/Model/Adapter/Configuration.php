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

namespace Customweb\RealexCw\Model\Adapter;

class Configuration implements \Customweb_Payment_IConfigurationAdapter
{
	/**
	 * @var \Customweb\RealexCw\Model\Configuration
	 */
	protected $_configuration;

	/**
	 * Store manager
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * Core store config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store = null;

    /**
     * @var \Magento\Store\Model\Website
     */
    private $website = null;

	public function __construct(
			\Customweb\RealexCw\Model\Configuration $configuration,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Sales\Model\Order\Config $orderConfig,
			\Magento\Framework\Encryption\EncryptorInterface $encryptor
	) {
		$this->_configuration = $configuration;
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_orderConfig = $orderConfig;
		$this->_encryptor = $encryptor;

		$this->store = $this->_storeManager->getStore();
		$this->website = $this->_storeManager->getWebsite();
	}

	/**
	 * @return \Magento\Store\Model\Store
	 */
	public function getStore() {
		return $this->store;
	}

	public function setDefaultStoreView() {
		$this->store = null;
		$this->website = null;
	}

	public function setStore(\Magento\Store\Model\Store $store) {
		$this->store = $store;
		$this->website = $store->getWebsite();
	}

	public function setWebsite(\Magento\Store\Model\Website $website) {
		$this->store = null;
		$this->website = $website;
	}

	public function getConfigurationValue($key, $language = null) {
		$key = \preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
		$rawValue = $this->_configuration->getConfigurationValue('realexcw/general', $key);
		if (\in_array($key, [
			
		])) {
			return $this->_encryptor->decrypt($rawValue);
		} else {
			return $rawValue;
		}
	}

	public function existsConfiguration($key, $language = null) {
		$key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
		return $this->_configuration->existsConfiguration('realexcw/general', $key);
	}

	public function getLanguages($currentStore = false) {
		$languages = [];
		if ($currentStore) {
			$locale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$languages[$locale] = new \Customweb_Core_Language($locale);
			return $languages;
		} else {
			$stores = $this->_storeManager->getStores();
			foreach ($stores as $store) {
				$locale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
				$languages[$locale] = new \Customweb_Core_Language($locale);
			}
			return $languages;
		}
	}

	public function getStoreHierarchy() {
		$hierarchy = null;
		if ($this->website != null || $this->store != null) {
			$hierarchy = [];
			if ($this->website != null) {
				$hierarchy[$this->website->getId()] = $this->website->getName();
			}
			if ($this->store != null) {
				$hierarchy[$this->store->getId()] = $this->store->getName();
			}
		}
		return $hierarchy;
	}

	public function useDefaultValue(\Customweb_Form_IElement $element, array $formData) {
		$controlName = implode('_', $element->getControl()->getControlNameAsArray());
		return (isset($formData['default'][$controlName]) && $formData['default'][$controlName] == 'default');
	}

	public function getOrderStatus() {
		$orderStatuses = [];
		foreach ($this->_orderConfig->getStatuses() as $key => $value) {
			$orderStatuses[$key] = __($value);
		}
		return $orderStatuses;
	}
}