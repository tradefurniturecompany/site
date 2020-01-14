<?php
namespace Hotlink\Framework\Model\Platform;

class Type
{

    //
    //  Defines the type coersions supported by the data objects for this platform
    //

    const MAGENTO = 'magento';
    const HOTLINKXML = 'hotlinkxml';

    // Types recognised
    protected static $_types = array( self::MAGENTO, self::HOTLINKXML );

    // Supported coersions to data objects
    protected static $_mappings = array( self::MAGENTO  => '_map_object_magento',
                                         self::HOTLINKXML => '_map_xml_hotlink' );

    public static function getTypes()
    {
        return self::$_types;
    }

    public static function getMappings()
    {
        return self::$_mappings;
    }

}
