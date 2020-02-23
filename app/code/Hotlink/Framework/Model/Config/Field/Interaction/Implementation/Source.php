<?php
namespace Hotlink\Framework\Model\Config\Field\Interaction\Implementation;

class Source
{

    protected $_interaction = false;

    protected $factory;
    protected $reflection;
    protected $map;

    function __construct(
        \Hotlink\Framework\Helper\Factory $factory,
        \Hotlink\Framework\Helper\Reflection $reflection,
        \Hotlink\Framework\Model\Config\Map $map
    )
    {
        $this->factory = $factory;
        $this->map = $map;
        $this->reflection = $reflection;
    }

    function setInteraction( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $this->_interaction = $interaction;
    }

    function toOptionArray( $multiselect = false )
    {
        $options = [];
        if ( $this->_interaction )
            {
                $implementations = $this->map->getImplementations( $this->_interaction );
                foreach ( $implementations as $class )
                    {
                        $implementation = $this->factory->create( $class );
                        $options[] = array( 'label' => $implementation->getName(),
                                            'value' => $class );
                    }
                $this->_interaction = false;
            }
        return $options;
    }

    function toOptionArrayFailed()
    {
        $options = array();
        if ( $this->interaction )
            {
                $implementations = $this->map->getImplementations( $this->interaction );
                foreach ( $implementations as $class )
                    {
                        $implementation = $this->factory->create( $class );
                        $class = $this->reflection->getClass( $implementation );
                        $options[] = array( 'label' => $implementation->getName(),
                                            'value' => $class );
                    }
                $this->_interaction = false;
            }
        return $options;
    }

}
