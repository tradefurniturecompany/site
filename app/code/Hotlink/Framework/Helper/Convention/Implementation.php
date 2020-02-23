<?php
namespace Hotlink\Framework\Helper\Convention;

class Implementation extends \Hotlink\Framework\Instance
{

    function __construct(
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        array $data = []
    )
    {
        // $args = get_defined_vars();
        // $this->__init( $args );
        // unset( $args[ 'moduleHelper' ] );
        // call_user_func_array( 'parent::__construct', $args );
        parent::__construct( get_defined_vars() );
    }

    function getClass( $interaction )
    {
        return $this->reflectionHelper->getClass( $interaction, '\Implementation' );
    }

}
