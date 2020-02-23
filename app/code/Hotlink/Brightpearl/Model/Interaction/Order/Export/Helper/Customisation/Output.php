<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation;

class Output extends \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\CustomisationAbstract
{

    function apply( $expression, $object )
    {
        if ( $expression )
            {
                $indices = explode( '.', $expression );
                while ( count( $indices ) > 1 )
                    {
                        $index = array_shift( $indices );
                        if ( array_key_exists( $index, $object->getData() ) )
                            {
                                $object = $object[ $index ];
                            }
                        else
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "missing index '$index'" ) );
                            }
                    }
                $index = array_shift( $indices );
                return [ 'object' => $object, 'index' => $index ];
            }
        throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "Empty output expression" ) );
    }

}
