<?php
namespace Hotlink\Brightpearl\Model\Trigger\Creditmemo\Created;

class Plugin
{

    protected $eventManager;

    function __construct( \Magento\Framework\Event\ManagerInterface $eventManager )
    {
        $this->eventManager = $eventManager;
    }

    function afterRefund( $subject, $result )
    {
        if ( $result instanceof \Magento\Sales\Model\Order\Creditmemo )
            {
                $this->eventManager->dispatch( 'hotlink_brightpearl_creditmemo_created_admin', [ 'creditmemo' => $result ] );
            }
    }

}
