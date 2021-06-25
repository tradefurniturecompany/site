<?php
namespace Hotlink\Framework\Model\Config\Structure\Initialise;

class Filter extends \Hotlink\Framework\Model\Config\Structure\Initialise\AbstractInitialise
{

    function apply( $config )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $map = $objectManager->get( \Hotlink\Framework\Model\Config\Map::class );
        foreach ( $map->getPlatforms() as $class )
            {
                $platform = $objectManager->get( $class );
                $config = $this->filter( $config, $platform->getConfigFilter() );
            }
        return $config;
    }

    protected function filter( $config, $paths )
    {
        if ( $paths )
            {
                $configPaths = array_map( [ $this, 'getConfigPath' ], $paths );
                foreach ( $configPaths as $configPath )
                    {
                        $config = $this->remove( $config, $configPath );
                    }
            }
        return $config;
    }

    //
    //  Remove the given config key
    //
    function remove( $config, $path )
    {
        $parts = explode( '/', $path );
        $deletekey = array_pop( $parts );
        $path = implode( '/', $parts );
        if ( $node = $this->extract( $config, $path ) )
            {
                if ( isset( $node[ $deletekey ] ) )
                    {
                        unset( $node[ $deletekey ] );
                        // now insert the node back into the config
                        $config = $this->insert( $config, $path, $node );
                    }
            }
        return $config;
    }

    //
    //  Replace the config key given by path
    //
    protected function insert( $config, $path, $node )
    {
        if ( ! is_array( $path ) )
            {
                $path = explode( '/', $path );
            }
        if ( count( $path ) )
            {
                $key = array_shift( $path );
                if ( isset( $config[ $key ] ) )
                    {
                        $config[ $key ] = $this->insert( $config[ $key ], $path, $node );
                    }
                return $config;
            }
        return $node;
    }

    protected function getConfigPath( $path )
    {
        $parts = explode( '/', $path );
        $path  = implode( '/children/', $parts );
        return 'config/system/sections/' . $path;
    }

}
