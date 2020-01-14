<?php
namespace Hotlink\Brightpearl\Model\Trigger\Creditmemo\Created;

class Plugin
{

    protected $eventManager;

    public function __construct( \Magento\Framework\Event\ManagerInterface $eventManager )
    {
        $this->eventManager = $eventManager;
    }

    public function afterRefund( $subject, $result )
    {
        if ( $result instanceof \Magento\Sales\Model\Order\Creditmemo )
            {
                $this->eventManager->dispatch( 'hotlink_brightpearl_creditmemo_created_admin', [ 'creditmemo' => $result ] );
            }
    }

}
