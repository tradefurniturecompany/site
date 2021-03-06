<?php
namespace Apptrian\ImageOptimizer\Cron;
class Scan {
	/**
	 * @var \Apptrian\ImageOptimizer\Helper\Data
	 */
	public $helper;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	public $logger;

	/**
	 * Constructor
	 *
	 * @param \Apptrian\ImageOptimizer\Helper\Data $helper
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	function __construct(
		\Apptrian\ImageOptimizer\Helper\Data $helper,
		\Psr\Log\LoggerInterface $logger
	) {
		$this->helper = $helper;
		$this->logger = $logger;
	}

	/**
	 * Cron method for executing scan and reindex process.
	 */
	function execute() {
		$extensionEnabled = (int) $this->helper->getConfig('apptrian_imageoptimizer/general/enabled');
		$cronJobEnabled = (int) $this->helper->getConfig('apptrian_imageoptimizer/cron/enabled_scan');
		if ($extensionEnabled && $cronJobEnabled) {
			try {
				$result = $this->helper->scanAndReindex();
				if ($result !== true) {
					$mPrefix = 'Image Optimizer Cron: Scan and Reindex process';
					$this->logger->debug($mPrefix . ' failed.');
				}
			} catch (\Exception $e) {
				$this->logger->critical($e);
			}
		}
	}
}