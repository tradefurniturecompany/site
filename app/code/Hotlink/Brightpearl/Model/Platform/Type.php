<?php
namespace Hotlink\Brightpearl\Model\Platform;

class Type extends \Hotlink\Framework\Model\Platform\Type
{
    const MAGEMODEL = 'magentomod';

    protected static $_types = array( self::MAGEMODEL );
    protected static $_mappings = array( self::MAGEMODEL => '_map_object_magento' );

    public static function getTypes()
    {
        return array_merge( parent::$_types, self::$_types );
    }

    public static function getMappings()
    {
        return array_merge( parent::$_mappings, self::$_mappings );
    }

}
