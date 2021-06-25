<?php
namespace Hotlink\Framework\Console;

class PlatformLocator
{

    protected static $classes = [];

    public static function register( $rootfile )
    {
        self::$classes[] = $rootfile;
    }

    public static function getClasses()
    {
        return self::$classes;
    }

    public static function getPlatforms()
    {
        $result = [];
        foreach ( self::getClasses() as $class )
            {
                $platform = \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
                if ( ! ( $platform instanceof \Hotlink\Framework\Model\Platform\AbstractPlatform ) )
                    {
                        throw new \Exception( "Platform class '$class' does not extend \Hotlink\Framework\Model\Platform\AbstractPlatform" );
                    }
                $result[] = $platform;
            }
        return $result;
    }

    public static function getPlatform( $code )
    {
        foreach ( self::getPlatforms() as $platform )
            {
                if ( $platform->getCode() == $code )
                    {
                        return $platform;
                    }
            }
        return false;
    }

}
