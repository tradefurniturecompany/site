<?php
namespace Hotlink\Framework\Helper\Config;

class Field
{

    public function serialize( $value, $k = 'key', $v = 'val' )
    {
        if ( is_array( $value ) )
            {
                $save = array();

                if ( is_null( $k ) && is_null( $v ) )
                    {
                        $value = serialize( $value );
                    }
                else
                    {
                        for ( $i = 0; $i < count( $value[ $k ] ); $i ++ )
                            {
                                $key = $value[ $k ][ $i ];
                                $val = $value[ $v ][ $i ];
                                $mapping = ( $key || $val )
                                    ? compact( $k, $v )
                                    : false;
                                if ( $mapping ) $save[] = $mapping;
                            }
                        $value = serialize( $save );
                    }
            }
        return $value;
    }

    public function unserialize( $string )
    {
        if ( !is_string( $string ) )
            {
                return $string;
            }
        $data = unserialize( $string );
        $data = ( $data ) ? $data : array();
        return $data;
    }

}
