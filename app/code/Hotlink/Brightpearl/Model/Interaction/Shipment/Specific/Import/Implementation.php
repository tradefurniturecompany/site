<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Shipment\Implementation\AbstractImplementation
{

    protected function _getName()
    {
        return 'Hotlink Brightpearl Shipping Importer (specific)';
    }

    public function execute()
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();
        $report( $env, "status" );

        $noteId   = $env->getParameter('goodsounote_id')->getValue();
        $noteType = $env->getParameter('note_type')->getValue();
        $notify = $env->getParameter('notify_customer')->getValue();

        if (is_null($noteId) || trim($noteId) == '') {
            $report->error("Goods-out note id is required");
            return;
        }

        $report->addReference($noteId);
        $tracking = $this->shipmentTrackingFactory->create()->loadWithType( $noteId, $noteType );

        if ( is_null($tracking->getId()) ) {

            $tracking->setBrightpearlId( $noteId );
            $tracking->setShipmentType( $noteType );

            $note = $this->apiGetOrderNotes( $noteType, null, $noteId );
            if ( $note && $this->checkNote($note) ) {
                if ( $bpOrder = $this->apiGetOrder( $note->getData('orderId') ) ) {
                    if ($orderRef = $bpOrder->getData('externalRef')) {
                        if ($mageOrder = $this->loadMagentoOrder( $orderRef )) {
                            $report->addReference( $mageOrder->getIncrementId() );
                            $this->importNote( $noteId, $tracking, $note, $bpOrder, $mageOrder, $notify );
                        }
                    }
                    else {
                        $report->warn("BP order is missing 'reference' field");
                    }
                }
            }
        }
        else {
            $report->debug( "Note already imported on ". $tracking->getCreatedAt() );
        }
    }
}
