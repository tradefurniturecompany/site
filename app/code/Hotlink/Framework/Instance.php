<?php
namespace Hotlink\Framework;

class Instance
{

    protected $injected = [];

    public function __construct( $args )
    {
        $this->injected = $args;
    }

    public function __get( $name )
    {
        return array_key_exists( $name, $this->injected ) ? $this->injected[ $name ] : null;
    }

}
