<?php
namespace Hotlink\Framework\Model\Platform;

abstract class AbstractPlatform
{

    /*
     *  This class identifies a platform, both for the web app and the cli.
     *  Therefore it does not use the OM, particularly as it is not available when launched from the cli.
     */

    abstract function getCode();
    abstract protected function _getName();
    abstract function getModulePath();

    abstract function getSection();
    abstract function getGroup();
    abstract function getField();

    function getConfigFilter()
    {
        return [];
    }

    protected function _getSingleton( $class )
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
    }

    function getName()
    {
        return __( $this->_getName() );
    }

    function getMap()
    {
        return $this->_getSingleton( '\Hotlink\Framework\Model\Config\Map' );
    }

    function getPath()
    {
        return implode( '/', [ $this->getSection(), $this->getGroup(), $this->getField() ] );
    }

    function getId()
    {
        $scopeConfig = $this->_getSingleton( '\Magento\Framework\App\Config\ScopeConfigInterface' );
        return $scopeConfig->getValue( $this->getPath(), 'default', null );
    }

    function getIdentifier()
    {
        $data = [ $this->getCode(). '[' . $this->getId() . ']', $this->getModuleName() . ':' . $this->getModuleVersion() ];
        return implode( ',', $data );
    }

    function getInteractions()
    {
        return $this->getMap()->getInteractions( $this );
    }

    function getModuleHelper()
    {
        return $this->_getSingleton( '\Hotlink\Framework\Helper\Module' );
    }

    function getModuleName()
    {
        return $this->getModuleHelper()->getName( $this );
    }

    function getModuleVersion()
    {
        return $this->getModuleHelper()->getVersion( $this->getModuleName() );
    }

    // Deprecated
    function getVersion()
    {
        return $this->getModuleVersion();
    }

}
