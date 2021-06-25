<?php
namespace Hotlink\Framework\Model\Schedule\Cron\Config;

class Plugin
{

	protected $handler;

	function __construct( \Hotlink\Framework\Model\Schedule\Cron\Inject $handler )
	{
		$this->handler = $handler;
	}

	function afterGetJobs( $subject, $result )
	{
		return $this->handler->execute( $result );
	}

}
