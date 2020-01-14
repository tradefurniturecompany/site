<?php
namespace Hotlink\Brightpearl\Controller\Webhook;

class Orderstatus extends \Hotlink\Brightpearl\Controller\Webhook\WebhookAbstract
{

    protected function _execute( $payload )
    {
        if ( array_key_exists( 'fullEvent', $payload ) )
            {
                $fullEvent = $payload[ 'fullEvent' ];
                if ( $fullEvent == \Hotlink\Brightpearl\Model\Platform\Brightpearl\Events::ORDER_MODIFIED_ORDER_STATUS )
                    {
                        $this->eventManager->dispatch( 'hotlink_brightpearl_order_status_modified_callback_received',  [ 'order' => $payload ] );
                    }
                else
                    {
                        return "skipped ($fullEvent)";
                    }
            }
        else
            {
                return "missing fullEvent";
            }
    }

}
