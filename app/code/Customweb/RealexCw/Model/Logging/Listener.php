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

namespace Customweb\RealexCw\Model\Logging;

use Monolog\Logger;

class Listener implements \Customweb_Core_Logger_IListener {

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @var string
	 */
	protected $fileName = '/var/log/realexcw.log';

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Framework\Filesystem\Driver\File $filesystem
	 */
	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Framework\Filesystem\Driver\File $filesystem
	) {
		$this->_scopeConfig = $scopeConfig;
		$this->_logger = new Logger('realexcw');
		$this->_logger->pushHandler(new Handler($filesystem));
	}

	public function addLogEntry($loggerName, $level, $message, \Exception $e = null, $object = null){
		if(!$this->isLevelActive($level)){
			return;
		}

		$content = '(' . $loggerName . ') ' . $message;

		if ($e !== null) {
			$content .= "\n";
			$content .= $e->getMessage();
			$content .= "\n";
			$content .= $e->getTraceAsString();
		}

		if ($object !== null) {
			ob_start();
			var_dump($object);
			$content .= "\n";
			$content .= ob_get_contents();
			ob_end_clean();
		}

		$this->_logger->log($this->getLogLevel($level), $content);
	}

	private function getLogLevel($level) {
		switch($level) {
			case \Customweb_Core_ILogger::LEVEL_DEBUG:
				return Logger::DEBUG;
			case \Customweb_Core_ILogger::LEVEL_INFO:
				return Logger::INFO;
			case \Customweb_Core_ILogger::LEVEL_ERROR:
				return Logger::ERROR;
			default:
				return Logger::INFO;
		}
	}

	private function isLevelActive($level) {
		switch ($this->_scopeConfig->getValue('realexcw/logging/level')) {
			case 'debug':
				return true;
			case 'info':
				if ($level == \Customweb_Core_ILogger::LEVEL_DEBUG) {
					return false;
				}
				return true;
			case 'error':
				if ($level == \Customweb_Core_ILogger::LEVEL_ERROR) {
					return true;
				}
				return false;
			default:
				return false;
		}
	}

}