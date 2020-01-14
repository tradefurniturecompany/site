<?php
namespace Hotlink\Brightpearl\Helper\Config;

class Backend
{
    public function serialize( $value )
    {
        return @serialize( $value );
    }

    public function unserialize( $value )
    {
        $arr = @unserialize( $value );

        if ( !is_array( $arr ) ) {
            return '';
        }

        $sortOrder = array();
        foreach ( $arr as $k => $val ) {

            if ( !is_array( $val ) ) {
                unset( $arr[ $k ] );
                continue;
            }
        }
        return $arr;
    }
}
