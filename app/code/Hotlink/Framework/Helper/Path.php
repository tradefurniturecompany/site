<?php
namespace Hotlink\Framework\Helper;

class Path
{
    protected $directoryList;

    public function __construct(\Magento\Framework\App\Filesystem\DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    public function absolute( $path, $type = 'base' )
    {
        $base = $this->directoryList->getPath( $type );
        if ( strpos( $path, $base ) === 0 )
            {
                $base = '';
            }
        $path = $this->join( $base, $path );
        return $path;
    }

    public function getFiles( $path, $glob )
    {
        return glob( $path . $glob );
    }

    public function rename( $from, $to )
    {
        return rename( $from, $to );
    }

    public function deleteFile( $name )
    {
        unlink( $name );
        return true;
    }

    public function exists( $name )
    {
        return ( is_dir( $name ) );
    }

    public function isReadable( $name )
    {
        return ( is_readable( $name ) );
    }

    public function isWriteable( $name )
    {
        return ( is_writeable( $name ) );
    }

    public function create( $name )
    {
        if ( ! $this->exists( $name ) )
            {
                mkdir( $name, 0777, true );
            }
    }

    public function clean( $path )
    {
        do
            {
                $old = $path;
                $path = str_replace( '.', '', $path );
                $path = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path );
            }
        while ( $old != $path );
        return $path;
    }

    //
    //  NB: This function does not respect Windows C:\\ syntax.
    //
    public function join()
    {
        $args = func_get_args();
        $paths = array();
        foreach ( $args as $arg )
            {
                $paths = array_merge( $paths, ( array ) $arg );
            }
        $paths2 = array();
        foreach ( $paths as $i => $path )
            {
                $path = trim( $path, DIRECTORY_SEPARATOR );
                if ( strlen( $path ) )
                    {
                        $paths2[] = $path;
                    }
            }
        $result = implode( DIRECTORY_SEPARATOR, $paths2 );     // If first element of old path was absolute, make this one absolute also
        if ( strlen( $paths[0] ) && substr( $paths[0], 0, 1 ) == DIRECTORY_SEPARATOR )
            {
                return DIRECTORY_SEPARATOR . $result;
            }
        return $this->clean( $result );
    }

}
