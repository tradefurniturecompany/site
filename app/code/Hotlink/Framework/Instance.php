<?php
namespace Hotlink\Framework;

class Instance
{

    protected $injected = [];

    function __construct( $args )
    {
        $this->injected = $args;
    }

    function __get( $name )
    {
        return array_key_exists( $name, $this->injected ) ? $this->injected[ $name ] : null;
    }

}
