<?php
namespace Hotlink\Framework\Helper\Convention;

class Data
{

    //
    //  Search for data object by appending _child to the passed object alias whilst searching up the inheritance chain.
    //

    protected $conventionCheckHelper;
    protected $reflectionHelper;
    protected $factory;

    //
    function __construct(
        \Hotlink\Framework\Helper\Convention\Check $conventionCheckHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Factory $factory
    ) {
        $this->conventionCheckHelper = $conventionCheckHelper;
        $this->reflectionHelper = $reflectionHelper;
        $this->factory = $factory;
    }

    function getModel( \Hotlink\Framework\Model\Api\Data $data, $child )
    {
        if ( strpos( $child, '\\' ) !== false )
            {
                if ( $this->conventionCheckHelper->exists( $child ) )
                    {
                        return $child;
                    }
            }

        $dataClass = $this->reflectionHelper->getClass( $data );

        $class = false;
        $found = false;
        while ( !$found && $dataClass )
            {
                $class = $this->reflectionHelper->getClass( $dataClass, $child );
                if ( $this->conventionCheckHelper->exists( $class ) )
                    {
                        $found = true;
                        break;
                    }
                $class = false;
                $dataClass = get_parent_class( $dataClass );
            }
        return $class;
    }

}
