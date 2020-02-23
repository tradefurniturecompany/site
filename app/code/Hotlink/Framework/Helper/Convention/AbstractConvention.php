<?php
namespace Hotlink\Framework\Helper\Convention;

class AbstractConvention
{

    protected $reflection;
    protected $check;

    function __construct(
        \Hotlink\Framework\Helper\Reflection $reflection,
        \Hotlink\Framework\Helper\Convention\Check $check
    )
    {
        $this->reflection = $reflection;
        $this->check = $check;
    }

    protected function _getValidClass( $thing, $append, $default )
    {
        $class = $this->reflection->getClass( $thing, $append );
        return $this->check->exists( $class )
            ? $class
            : $default;
    }

}
