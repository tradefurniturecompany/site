<?php
namespace Hotlink\Brightpearl\Controller\Webhook;

class Goodsout extends \Hotlink\Brightpearl\Controller\Webhook\WebhookAbstract
{

    protected function _execute( $payload )
    {
        if ( array_key_exists( 'resourceType', $payload ) )
            {
                $resourceType = $payload[ 'resourceType' ];
                if ( $resourceType == \Hotlink\Brightpearl\Model\Platform\Brightpearl\Events::GOODS_OUT_NOTE )
                    {
                        $this->eventManager->dispatch( 'hotlink_brightpearl_shipping_goods_out_callback_received',  [ 'note' => $payload ] );
                    }
                else
                    {
                        return "skipped ($resourceType)";
                    }
            }
        else
            {
                return "missing resourceType";
            }
    }

}
