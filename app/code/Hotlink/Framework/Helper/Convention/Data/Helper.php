<?php
namespace Hotlink\Framework\Helper\Convention\Data;

class Helper
{

    protected $reflectionHelper;
    protected $conventionCheckHelper;
    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Convention\Check $conventionCheckHelper
    )
    {
        $this->factoryHelper = $factoryHelper;
        $this->reflectionHelper = $reflectionHelper;
        $this->conventionCheckHelper = $conventionCheckHelper;
    }

    public function getInstance( \Hotlink\Framework\Model\Api\Data $data )
    {
        $dataClass = $this->reflectionHelper->getClass( $data );
        $object = false;
        while ( !$object && $dataClass )
            {
                $class = $this->reflectionHelper->getClass( $dataClass, 'Helper' );
                //
                //  Dangerous: Alternative is to fill the log with junk errors
                //
                if ( $this->conventionCheckHelper->exists( $class ) )
                    {
                        $object = $this->factoryHelper->create( $model );
                        if ( ! ( $object instanceof \Hotlink\Framework\Model\Api\Data\Helper ) )
                            {
                                $object = false;
                            }
                    }
                $dataClass = get_parent_class( $dataClass );
            }
        return $object;
    }

}