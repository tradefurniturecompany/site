<?php
namespace Hotlink\Framework\Model\Platform;

abstract class AbstractPlatform
{

    /*
     *  This class identifies a platform, both for the web app and the cli.
     *  Therefore it does not use the OM, particularly as it is not available when launched from the cli.
     */

    abstract public function getCode();
    abstract protected function _getName();
    abstract public function getModulePath();

    abstract public function getSection();
    abstract public function getGroup();
    abstract public function getField();

    public function getConfigFilter()
    {
        return [];
    }

    protected function _getSingleton( $class )
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
    }

    public function getName()
    {
        return __( $this->_getName() );
    }

    public function getMap()
    {
        return $this->_getSingleton( '\Hotlink\Framework\Model\Config\Map' );
    }

    public function getPath()
    {
        return implode( '/', [ $this->getSection(), $this->getGroup(), $this->getField() ] );
    }

    public function getId()
    {
        $scopeConfig = $this->_getSingleton( '\Magento\Framework\App\Config\ScopeConfigInterface' );
        return $scopeConfig->getValue( $this->getPath(), 'default', null );
    }

    public function getIdentifier()
    {
        $data = [ $this->getCode(). '[' . $this->getId() . ']', $this->getModuleName() . ':' . $this->getModuleVersion() ];
        return implode( ',', $data );
    }

    public function getInteractions()
    {
        return $this->getMap()->getInteractions( $this );
    }

    public function getModuleHelper()
    {
        return $this->_getSingleton( '\Hotlink\Framework\Helper\Module' );
    }

    public function getModuleName()
    {
        return $this->getModuleHelper()->getName( $this );
    }

    public function getModuleVersion()
    {
        return $this->getModuleHelper()->getVersion( $this->getModuleName() );
    }

    // Deprecated
    public function getVersion()
    {
        return $this->getModuleVersion();
    }

}
