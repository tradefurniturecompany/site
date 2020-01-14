<?php
namespace Hotlink\Framework\Model\Interaction\Implementation;

abstract class AbstractImplementation extends \Hotlink\Framework\Model\AbstractModel implements \Hotlink\Framework\Model\Report\IReport
{

    protected $_interaction = false;

    abstract protected function _getName();
    abstract public function execute();

    public function getInteraction()
    {
        return $this->_interaction;
    }

    public function setInteraction( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $this->_interaction = $interaction;
        return $this;
    }

    public function getName()
    {
        return __( $this->_getName() );
    }

    public function getTrigger()
    {
        return $this->getInteraction()->getTrigger();
    }

    public function getEnvironments()
    {
        return $this->getInteraction()->getEnvironments();
    }

    public function getEnvironment( $storeId = null )
    {
        return $this->getInteraction()->getEnvironment( $storeId );
    }

    public function createEnvironment( $storeId )
    {
        return $this->getInteraction()->createEnvironment( $storeId );
    }

    public function hasEnvironment( $storeId = null )
    {
        return $this->getInteraction()->hasEnvironment( $storeId );
    }

    //
    //  IReport
    //
    public function getReportSection()
    {
        return 'implementation';
    }

}
