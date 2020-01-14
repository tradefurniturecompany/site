<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Implementation;

abstract class AbstractImplementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{
    
    protected function getOrCreateEnvironment($storeId)
    {
        return $this->hasEnvironment($storeId)
            ? $this->getEnvironment($storeId)
            : $this->createEnvironment($storeId);
    }

    /*
    protected function reportTracking($message, $tracking)
    {
        $report = $this->getReport();

        $fields = array();
        foreach ($tracking->getData() as $name => $value) {
            $fields[] = $name."=".( is_null($value) ? 'null' : $value);
        }

        $report
            ->debug($message)
            ->indent()
            ->debug(implode(", ", $fields))
            ->unindent();
    }

    protected function updateTracking($tracking, $inBP, $sendToBP, $sentAt = null)
    {
        $tracking
            ->setInBp( $inBP )
            ->setSendToBp( $sendToBP );

        if ($sentAt !== null)
            $tracking->setSentAt( $sentAt );

        $tracking->save();
    }
    */

}