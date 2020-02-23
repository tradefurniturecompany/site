<?php
namespace Hotlink\Framework\Helper\Convention;

class Check
{

    //
    //  Tests if classes exist without causing Magento to throw exceptions or log errors
    //
    // function modelExists( $name )
    // {
    //     $name = Mage::getConfig()->getModelClassName( $name );
    //     return $this->_exists( $name );
    // }

    // function helperExists( $name )
    // {
    //     $name = Mage::getConfig()->getHelperClassName( $name );
    //     return $this->_exists( $name );
    // }

    // function blockExists( $name )
    // {
    //     $name = Mage::getConfig()->getBlockClassName( $name );
    //     return $this->_exists( $name );
    // }

    function exists( $name )
    {
        $original = set_error_handler( "\Hotlink\Framework\Helper\Convention\Check::ignoreClassLoadErrors" );
        $result = class_exists( $name, true );
        set_error_handler( $original );
        return $result;
    }

    public static function ignoreClassLoadErrors()
    {
    }

}
