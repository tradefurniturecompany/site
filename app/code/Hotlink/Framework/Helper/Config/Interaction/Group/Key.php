<?php
namespace Hotlink\Framework\Helper\Config\Interaction\Group;

class Key
{
    protected $reflectionHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Reflection $reflectionHelper
    )
    {
        $this->reflectionHelper = $reflectionHelper;
    }

    public function encode( $thing )
    {
        $class = $this->reflectionHelper->getClass( $thing, null, false );
        return str_replace( '\\', '_', $class );
    }

    public function decode( $string )
    {
        $psr4 = str_replace( '_', '\\', $string );
        $class = $this->reflectionHelper->getClass( $psr4 );
        return $class;
    }

}
