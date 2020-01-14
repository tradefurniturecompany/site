<?php
namespace Hotlink\Framework\Helper;

class Html
{

    protected $reflection;

    public function __construct( \Hotlink\Framework\Helper\Reflection $reflection )
    {
        $this->reflection = $reflection;
    }

    public function getHtmlNameOld( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = strtolower( $name );
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    public function getHtmlName2( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        if ( substr( $name, 0, 1 ) !== '\\' )
            {
                $name = '\\' . $name;
            }
        return $name;
    }

    public function getHtmlName( $thing )
    {
        $name = 
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    public function encode( $thing )
    {
        $name = $this->reflection->getClass( $thing );
        $name = str_replace( '\\', '-', $name );
        if ( substr( $name, 0, 1 ) == '-' )
            {
                $name = substr( $name, 1 );
            }
        return $name;
    }

    public function decode( $thing )
    {
        $name = is_object( $thing ) ? get_class( $thing ) : ( string ) $thing;
        $name = $this->reflection->getClass( $thing );
        $name = str_replace( '-', '\\', $name );
        if ( substr( $name, 0, 1 ) != '\\' )
            {
                $name = '\\' . $name;
            }
        return $name;
    }

}
