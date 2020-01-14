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

namespace Customweb\RealexCw\Model\Cron;

class Processor
{
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $_logger;

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 */
	public function __construct(
			\Psr\Log\LoggerInterface $logger,
			\Customweb\RealexCw\Model\DependencyContainer $container
	) {
		$this->_logger = $logger;
		$this->_container = $container;
	}

	/**
	 * @return void
	 */
	public function execute()
	{
		try {
			$packages = array(
			0 => 'Customweb_Realex',
 			1 => 'Customweb_Payment_Authorization',
 		);
			$packages[] = 'Customweb_Payment_Update_ScheduledProcessor';
			$cronProcessor = new \Customweb_Cron_Processor($this->_container, $packages);
			$cronProcessor->run();
		} catch (\Exception $e) {
			$this->_logger->error('Error in RealexCw cron processor: ' . $e->getMessage());
		}
	}
}