<?php
namespace Hotlink\Framework\Model\Config;

abstract class AbstractConfig extends \Magento\Framework\DataObject
{

    abstract protected function _getSection();
    abstract protected function _getGroup();

    protected $_serialized = array();
    protected $_maps = array();
    protected $_csv = array();
    protected $_configPath = false;
    protected $_section = false;
    protected $_group = false;

    protected $storeManager;
    protected $scopeConfig;
    protected $interactionConfigFieldHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Config\Field $interactionConfigFieldHelper,
        array $data = []
        )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->interactionConfigFieldHelper = $interactionConfigFieldHelper;
        parent::__construct( $data );
    }

    public function getPath( $key = null )
    {
        $parts = [ $this->getSection(), $this->getGroup() ];
        if ( ! is_null( $key ) )
            {
                $parts[] = $key;
            }
        return implode( '/', $parts );
    }

    public function getSection()
    {
        if ( !$this->_section )
            {
                $this->_section = $this->_getSection();
            }
        return $this->_section;
    }

    public function getGroup()
    {
        if ( !$this->_group )
            {
                $this->_group = $this->_getGroup();
            }
        return $this->_group;
    }

    public function getScopeType()
    {
        return \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    public function getScope( $storeId )
    {
        if ( is_null( $storeId ) )
            {
                $storeId = $this->storeManager->getStore()->getCode();
            }
        return $storeId;
    }

    protected function getStoreKey( $storeId, $path )
    {
        return ( ( is_null( $storeId ) ) ? "null" : $storeId ) . "_" . $path;
    }

    public function getData( $path='', $index=null )
    {
        if ( array_key_exists( $path, $this->_data ) )
            {
                return $this->_data[ $path ];
            }
        return null;
    }

    // Get store config data (omit optional parameters for current store)
    public function getConfigData( $key, $storeId=null, $default=false )
    {
        return $this->getConfigPathData( $this->getPath( $key ), $storeId, $default );
    }

    public function getConfigPathData( $path, $storeId=null, $default=false )
    {
        $unique = $this->getStoreKey( $storeId, $path );
        if ( !$this->hasData( $unique ) )
            {
                $value = $this->scopeConfig->getValue( $path, $this->getScopeType(), $this->getScope( $storeId ) );
                if ( is_null( $value ) || false===$value || ( '' == $value ) )
                    {
                        $value = $default;
                    }
                $this->setData( $unique, $value );
            }
        return $this->getData( $unique );
    }

    // Get store config data (omit optional parameters for current store)
    public function setConfigData( $key, $storeId, $value )
    {
        return $this->setConfigPathData( $this->getPath( $key ), $storeId, $value );
    }

    // Get store config data (omit optional parameters for current store)
    public function setConfigPathData( $path, $storeId, $value )
    {
        $unique = $this->getStoreKey( $storeId, $path );
        $this->setData( $unique, $value );
        return $this;
    }

    //
    //  Returns a multidimensional array key value pairs
    //
    public function getSerializedField( $key, $storeId, $default = '' )
    {
        return $this->getSerializedPathField( $this->getPath( $key ), $storeId, $default );
    }

    public function getSerializedPathField( $path, $storeId, $default = '' )
    {
        $unique = $this->getStoreKey( $storeId, $path );
        if ( !array_key_exists( $unique, $this->_serialized ) )
            {
                $data = $this->getConfigPathData( $path, $storeId, $default );
                $this->_serialized[ $unique ] = $this->interactionConfigFieldHelper->unserialize( $data );
            }
        return $this->_serialized[ $unique ];
    }

    //
    //  Returns a one dimensional array of key => val, generated from a serialised array structure
    //
    public function getSerializedFieldFlat( $key, $storeId, $keyField = 'key', $valField = 'val' )
    {
        return $this->getSerializedPathFieldFlat( $this->getPath( $key ), $storeId, $keyField, $valField );
    }

    public function getSerializedPathFieldFlat( $path, $storeId, $keyField = 'key', $valField = 'val' )
    {
        $result = [];
        $data = $this->getSerializedPathField( $path, $storeId, array() );
        foreach ( $data as $item )
            {
                if ( is_array( $item ) )
                    {
                        if ( array_key_exists( $keyField, $item ) && array_key_exists( $valField, $item ) )
                            {
                                $result[ $item[ $keyField ] ] = $item[ $valField ];
                            }
                    }
            }
        return $result;
    }

    // protected function lookupSerializedFieldValue( $path, $value, $storeId, $default = "" )
    // {
    //     $map = $this->getSerializedFieldFlat( $path, $storeId );
    //     if ( array_key_exists( $value, $map ) )
    //         {
    //             return $map[ $value ];
    //         }
    //     return $default;
    // }

    protected function getCsvField( $key, $storeId = null, $default = '')
    {
        return $this->getCsvPathField( $this->getPath( $key ), $storeId, $default );
    }

    protected function getCsvPathField( $path, $storeId = null, $default = '' )
    {
        $unique = $this->getStoreKey( $storeId, $path );
        if ( !array_key_exists( $unique, $this->_csv ) )
            {
                $data = $this->getConfigPathData( $path, $storeId, '' );
                $data = ( $data ) ? explode( ',', $data ) : array();
                if ( count ( $data ) > 0 )
                    {
                        $data = array_combine( $data, $data );
                    }
                $this->_csv[ $unique ] = $data;
            }
        return $this->_csv[ $unique ];
    }

}
