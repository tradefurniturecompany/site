<?php
namespace Hotlink\Framework\Model\Schedule\Cron\Config;

class Plugin
{

    protected $handler;

    public function __construct( \Hotlink\Framework\Model\Schedule\Cron\Inject $handler )
    {
        $this->handler = $handler;
    }

    public function afterGetJobs( $subject, $result )
    {
        return $this->handler->execute( $result );
    }

}
