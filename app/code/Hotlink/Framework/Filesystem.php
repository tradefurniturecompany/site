<?php
namespace Hotlink\Framework;

class Filesystem
{

    static function getRelativePath( $from, $down = [], $up = 0 )
    {
        $parts = explode( DIRECTORY_SEPARATOR, $from );
        if ( count( $parts ) > 0 )
            {
                if ( self::isFilename( $from ) )
                    {
                        array_pop( $parts );
                    }
            }
        for ( $i = 1; $i <= $up; $i++ )
            {
                array_pop( $parts );
            }
        if ( !is_array( $down ) )
            {
                $down = $down ? [ $down ] : [];
            }
        foreach ( $down as $item )
            {
                array_push( $parts, $item );
            }
        $path = implode( '/', $parts );
        return $path;
    }

    static function isFilename( $thing )
    {
        return substr( $thing, -4, 1 ) == '.';
    }

}
