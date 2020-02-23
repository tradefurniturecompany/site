<?php
namespace Hotlink\Framework\Model\Interaction\Implementation;

abstract class AbstractImplementation extends \Hotlink\Framework\Model\AbstractModel implements \Hotlink\Framework\Model\Report\IReport
{

    protected $_interaction = false;

    abstract protected function _getName();
    abstract function execute();

    function getInteraction()
    {
        return $this->_interaction;
    }

    function setInteraction( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $this->_interaction = $interaction;
        return $this;
    }

    function getName()
    {
        return __( $this->_getName() );
    }

    function getTrigger()
    {
        return $this->getInteraction()->getTrigger();
    }

    function getEnvironments()
    {
        return $this->getInteraction()->getEnvironments();
    }

    function getEnvironment( $storeId = null )
    {
        return $this->getInteraction()->getEnvironment( $storeId );
    }

    function createEnvironment( $storeId )
    {
        return $this->getInteraction()->createEnvironment( $storeId );
    }

    function hasEnvironment( $storeId = null )
    {
        return $this->getInteraction()->hasEnvironment( $storeId );
    }

    //
    //  IReport
    //
    function getReportSection()
    {
        return 'implementation';
    }

}
