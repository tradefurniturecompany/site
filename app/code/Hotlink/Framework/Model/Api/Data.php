<?php
namespace Hotlink\Framework\Model\Api;

class Data extends \Magento\Framework\DataObject implements \IteratorAggregate, \Countable, \Hotlink\Framework\Model\Report\IReport
{

    const TOSTRING_FORMAT_NONE = 'none';
    const TOSTRING_FORMAT_XML  = 'xml';

    //
    //  This class is intended to be easy to read and quick to use, allowing simplistic logic and validation to be built
    //  into data structures.
    //  Supports the building of data object using array indexers.
    //

    //
    //  Type specific mapping functions should be declared on derived classes to support speciliased castings.
    //
    //  If a specific mapping method existings supporting the type, it will be used, otherwise the inference method is used.
    //
    //  The convention for function names is to specify the language type first (e.g. object, xml) followed by the
    //  platform centric case or format (e.g. magento, hotlink)
    //
    //  Example signatures:
    //     protected function _map_object_magento( Mage_Catalog_Model_Product $product );
    //     protected function _map_xml_hotlink( SimpleXmlElement $xmlHotlinkProduct );
    //
    //  WARNING: Although these signatures can (and should be) be stricly declared, PHP ignores and executes
    //  mismatched signatures at runtime when strict mode is disabled, and continues execution after generating
    //  a warning. This bypassing of signature enforcement is unexpected and potentially introduces a new source
    //  of bugs, but is no less dangerous than any other incorrect call (just that the problem goes undetected and
    //  yields a more severe error and obscure bug as processing continues).
    //

    const INDENT = 3;

    protected $_validator = null;
    protected $_helper = null;
    protected $_type = false;
    protected $_report = null;
    protected $_parent = null;
    protected $_childClasses = [];

    protected $reportFactory;
    protected $exceptionHelper;
    protected $conventionDataHelperHelper;
    protected $configMap;
    protected $conventionDataHelper;
    protected $dataFactory;
    protected $xmlFactory;
    protected $factoryHelper;

    function __construct(
        \Magento\Framework\Simplexml\ElementFactory $xmlFactory,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Convention\Data\Helper $conventionDataHelperHelper,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Helper\Convention\Data $conventionDataHelper,
        \Hotlink\Framework\Model\Api\DataFactory $dataFactory,
        array $data = []
    )
    {
        $this->factoryHelper = $factoryHelper;
        $this->xmlFactory = $xmlFactory;
        $this->reportFactory = $reportFactory;
        $this->exceptionHelper = $exceptionHelper;
        $this->conventionDataHelperHelper = $conventionDataHelperHelper;
        $this->configMap = $configMap;
        $this->conventionDataHelper = $conventionDataHelper;
        $this->dataFactory = $dataFactory;
        parent::__construct(
            $data
        );
    }

    function getName()
    {
        $class = get_class( $this );
        $name = strtolower( $class );
        return $name;
    }

    function getKey()
    {
        $class = get_class( $this );
        $parts = explode( '_', $class );
        return array_pop( $parts );
    }

    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->reportFactory->create() );
            }
        return $this->_report;
    }

    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    function getReportSection()
    {
        return 'data';
    }

    function map( $data, $type = false, $extra = false )
    {
        $this->_type = $type;
        $methods = array( '_map', '_map_inference' );
        if ( $method = $this->getMappingMethod( $type ) )
            {
                array_unshift( $methods, $method );
            }
        foreach ( $methods as $map )
            {
                if ( method_exists( $this, $map ) )
                    {
                        // Make $extra parameter to be optional with call_user_func_array
                        call_user_func_array( array( $this, $map ), array( $data, $extra ) );
                        return $this;
                    }
            }
        $this->exceptionHelper->throwProcessing( 'No suitable map method found.' );
    }

    //
    //  Returns a hash of (type, methodname) to support different mappings.
    //  Overload to support multiple types.
    //
    function getMappings()
    {
        return array();
    }

    protected function getMappingMethod( $type )
    {
        $types = $this->getMappings();
        return isset( $types[ $type ] ) ? $types[ $type ] : false;
    }

    protected function getObject( $data, $classOrKey = false, $throwValidationErrors = true, $extra = false, $type = false )
    {
        $object = false;
        if ( $model = $this->getChildClass( $classOrKey ) )
            {
                $object = $this->factoryHelper->create( $model );
                if ( $object instanceof \Hotlink\Framework\Model\Api\Data )
                    {
                        $object->setParent( $this );
                        $object->setHelper( $this->getHelper() );
                        if ( $type === false )
                            {
                                $type = $this->_type;
                            }
                        $report = $this->getReport();
                        $report->nostacktrace();
                        $report( $object, 'map', $data, $type, $extra );
                    }
            }
        return $object;
    }

    //
    //  Prefix text with the data object xpath
    //
    protected function annotate( $text )
    {
        $path = '';
        $traverse = $this;
        while ( $traverse )
            {
                $path = $traverse->getKey() . '/' . $path;
                $traverse = $traverse->getParent();
            }
        $path = trim( $path, '/' );
        return $path . ': ' . $text;
    }

    protected function throwValidationException( $message, \Exception $prev = null )
    {
        $this->exceptionHelper->throwValidation( $message, $this, $prev );
    }

    //
    //   Used to filter simple values or objects.
    //
    function filterObjects( $include = false )
    {
        $result = array();
        foreach ( $this as $key => $value )
            {
                if ( is_object( $value ) )
                    {
                        if ( $include )
                            {
                                $result[ $key ] = $value;
                            }
                    }
                else
                    {
                        if ( !$include )
                            {
                                $result[ $key ] = $value;
                            }
                    }
            }
        return $result;
    }

    function getParent()
    {
        return $this->_parent;
    }

    function setParent( \Hotlink\Framework\Model\Api\Data $object = null )
    {
        $this->_parent = $object;
        return $this;
    }

    //
    //  Finds elements down the tree using a simplistic xpath syntax - does not search globally or up, or use filtering criteria.
    //  Supports / delineators.
    //  Support * wildcards.
    //  Leading / skips starting object
    //
    function xpath( $path )
    {
        $results = array();
        $parts = explode( '/', $path );
        $searchKey = $parts[ 0 ];

        $searchList = false;
        if ( $searchKey == '*' )
            {
                $searchList = $this;
            }
        else
            {
                if ( isset( $this[ $searchKey ] ) )
                    {
                        if ( $this[ $searchKey ] instanceof \Hotlink\Framework\Model\Api\Data )
                            {
                                if ( count( $parts ) == 1 )
                                    {
                                        $results[] = $this[ $searchKey ];
                                    }
                                else
                                    {
                                        $searchList[] = $this[ $searchKey ];
                                    }
                            }
                        else
                            {
                                $results[] = $this[ $searchKey ];
                            }
                    }
            }
        if ( $searchList )
            {
                array_shift( $parts );
                $search = implode( '/', $parts );
                foreach ( $searchList as $item )
                    {
                        if ( $item instanceof \Hotlink\Framework\Model\Api\Data )
                            {
                                foreach ( $item->xpath( $search ) as $result )
                                    {
                                        $results[] = $result;
                                    }
                            }
                    }
            }
        return $results;
    }

    //
    //  Use to pass configuration parameters and/or utility functions deep into the objects
    //
    function getHelper()
    {
        if ( is_null( $this->_helper ) )
            {
                $this->_helper = $this->conventionDataHelperHelper->getInstance( $this );
            }
        return $this->_helper;
    }

    function setHelper( $object )
    {
        if ( $this->_helper )
            {
                $this->exceptionHelper->throwProcessing( 'Data object helper cannot be reassigned' );
            }
        $this->_helper = $object;
        return $this;
    }

    function toString( $format='' )
    {
        if ( !$format )
            {
                $format = $this->getHelper()->getFormat();
            }
        switch ( $format )
            {
            case self::TOSTRING_FORMAT_XML:
                return $this->asXmlString();
                break;
            case self::TOSTRING_FORMAT_NONE:
                break;
            default:
                break;
            }
        return parent::toString();
    }

    protected function _map_inference( $data )
    {
        $applied = false;
        if ( $data instanceof \stdClass )
            {
                $applied = $this->_map_array( get_object_vars( $data ) );
            }
        else if ( $data instanceof \SimpleXMLElement )
            {
                $applied = $this->_map_xml( $data );
            }
        else if ( is_array( $data ) || ( $data instanceof Traversable ) )
            {
                $applied = $this->_map_array( $data );
            }
        if ( !$applied )
            {
                $name = ( is_object( $data ) ) ? get_class( $data ) : gettype( $data );
                $this->exceptionHelper->throwProcessing( 'Could not automatically map data of type ' . $name . ' in [class], implement an appropriate _map method to support this type', $this );
            }
        return $applied;
    }

    //
    //  For a given key, identifies a data object model to be used.
    //
    function setChildClass( $key, $model )
    {
        $this->_childClasses[ $key ] = $model;
        return $this;
    }

    function getChildClass( $key )
    {
        if ( $model = $this->getChildClassAssigned( $key ) )
            {
                return $model;
            }
        if ( $model = $this->getChildClassConfig( $key ) )
            {
                return $model;
            }
        if ( $model = $this->getChildClassConvention( $key ) )
            {
                return $model;
            }
        return $this->getChildClassDefault( $key );
    }

    function getChildClassAssigned( $key )
    {
        if ( array_key_exists( $key, $this->_childClasses ) )
            {
                return $this->_childClasses[ $key ];
            }
        return false;
    }

    function getChildClassConfig( $key )
    {
        $dataobjects = $this->configMap->getDataobjects();
        $name = $this->getName() . '\\' . $key;
        return ( isset( $dataobjects[ $name ] ) )
            ? $dataobjects[ $name ]
            : false;
    }

    function getChildClassConvention( $key )
    {
        return $this->conventionDataHelper->getModel( $this, $key );
    }

    function getChildClassDefault( $key )
    {
        return '\Hotlink\Framework\Model\Api\Data';
    }

    //
    //  To map attributes, you should provide a custom xml mapping function
    //  This is because the resulting array structure will not be consistent when attributes are omitted, eg:
    //
    //    <example>test</example>
    //    requires a simple key type;  $this[ 'example' ] = "test"
    //
    //    <example attr="hello">test</example>
    //    requires a complex key type; $this[ 'example' ] = \Hotlink\Framework\Model\Api\Data object
    //
    protected function _map_xml( $xml, $forceUniqueKeys = false )
    {
        $isCollection = ( count( $xml ) > 1 );
        if ( $isCollection )
            {
                //
                //  Calculate the count of each tag name
                //
                $keys = array();
                foreach ( $xml as $key => $value )
                    {
                        if ( !isset( $keys[ $key ] ) )
                            {
                                $keys[ $key ] = array();
                            }
                        $keys[ $key ][] = $value;
                    }

                //
                //  When a tag is repeated, map onto a child object, otherwise map a simple hash value (if possible)
                //
                foreach ( $keys as $key => $values )
                    {
                        if ( count( $values ) == 1 )
                            {
                                $this->_map_xml( $values[ 0 ], $forceUniqueKeys );
                            }
                        else
                            {
                                $this[ $key ] = $this->factoryHelper->create( $this->getChildClass( $key ) )
                                    ->setParent( $this )
                                    ->map( $values );
                            }
                    }
            }
        else
            {
                $key = $xml->getName();
                $isComplex = ( count( $xml->children() ) > 0 );
                if ( $isComplex )
                    {
                        $this[ $key ] = $this->factoryHelper->create( $this->getChildClass( $key ) )
                            ->setParent( $this )
                            ->map( $xml->children() );
                    }
                else
                    {
                        $this[ $key ] = ( string ) $xml;
                    }
            }
        return true;
    }

    //
    //   This implementation omits the root tag, and has a bug that fails to extract the tag value when attributes are present.
    //
    protected function _map_xml_deprecated( $xml, $forceUniqueKeys = false )
    {
        foreach ( $xml->attributes() as $key => $value )
            {
                $this[ $key ] = ( string ) $value;
            }
        //
        //  When a key is repeated, map onto a child object, otherwise map a simple hash value
        //
        //  Calculate count of keys
        //
        $keys = array();
        foreach ( $xml as $key => $value )
            {
                if ( !isset( $keys[ $key ] ) )
                    {
                        $keys[ $key ] = array();
                    }
                $keys[ $key ][] = $value;
            }
        foreach ( $keys as $key => $values )
            {
                if ( count( $values ) == 1 )
                    {
                        $value = $values[ 0 ];
                        $this[ $key ] = ( $value->children() )
                            ? $this->dataFactory->create()->map( $value )
                            : ( string ) $value;
                    }
                else
                    {
                        $this[ $key ] = $this->dataFactory->create()->map( $values );
                    }
            }
        return true;
    }

    protected function _map_array( $arr, $forceUniqueKeys = false )
    {
        foreach ( $arr as $key => $value )
            {
                if ( $forceUniqueKeys && array_key_exists( $key, $this ) )
                    {
                        $this->exceptionHelper->throwProcessing( 'Item ' . $key . ' has already been defined in [class]', $this );
                    }
                if ( ( $value instanceof \SimpleXMLElement ) && ( count ( $value ) == 0 ) && ( count( $value->attributes() ) == 0 ) )
                    {
                        $this[ $key ] = ( string ) $value;
                    }
                else if ( is_object( $value ) || is_array( $value ) )
                    {
                        $object = $this->factoryHelper->create( $this->getChildClass( $key ) )
                            ->setParent( $this )->map( $value );

                        $this[ $key ] = $object;
                    }
                else
                    {
                        $this[ $key ] = $value;
                    }
            }
        return true;
    }

    protected function spacing( $indent )
    {
        return ( $indent == 0 ) ? '' : str_pad( ' ', ( $indent * self::INDENT ) );
    }

    function asXmlString( array $attributes=array(), $tagName='root', $intName='item', $indent=0, $addHeader=false, $addCdata=false )
    {
        $atts = array();
        $tags = array();
        foreach ( $this->getData() as $key => $value )
            {
                if ( !is_integer( $key ) && in_array( $key, $attributes ) )
                    {
                        $atts[ $key ] = $value;
                    }
                else
                    {
                        $tags[ $key ] = $value;
                    }
            }

        $xmlModel = $this->xmlFactory->create( '<node></node>' );

        $inner = '';
        if ( !empty( $tagName ) )
            {
                $indent++;
            }

        foreach ( $tags as $key => $value )
            {
                if ( is_integer( $key ) )
                    {
                        $key = $intName;
                    }
                if ( $value instanceof \Hotlink\Framework\Model\Api\Data )
                    {
                        $inner .= $value->asXmlString( array(), $key, $intName, $indent, false, $addCdata );
                    }
                else
                    {
                        if ( false !== strpos( $value, ']]>' ) )
                            {
                                $value = $xmlModel->xmlentities( $value );
                            }
                        else
                            {
                                if ( $value !== $xmlModel->xmlentities( $value ) )
                                    {
                                        if ( $addCdata === true )
                                            {
                                                $value = "<![CDATA[$value]]>";
                                            }
                                        else
                                            {
                                                $value = $xmlModel->xmlentities( $value );
                                            }
                                    }
                            }
                        $inner .= $this->spacing( $indent ) . "<$key>$value</$key>\n";
                    }
            }

        $opening = ( $addHeader ) ? '<?xml version="1.0" encoding="UTF-8"?>'."\n" : '';
        $closing = '';
        if ( !empty( $tagName ) )
            {
                $indent--;
                $opening .= "<$tagName";
                foreach ( $atts as $key => $value )
                    {
                        $opening .= " $key=\"" . $xmlModel->xmlentities( $value ) . "\"";
                    }
                $opening = $this->spacing( $indent ) . $opening;
                if ( strlen( $inner ) > 0 )
                    {
                        $opening .= ">";
                        $closing = $this->spacing( $indent ) . "</$tagName>\n";
                    }
                else
                    {
                        $opening .= " />";
                    }
            }
        if ( ( strlen( $inner ) > 0 ) || ( strlen( $opening ) > 0 ) )
            {
                $opening = $opening . "\n";
            }
        return $opening . $inner . $closing;
    }

    //
    //  interface IteratorAggregate
    //
    function getIterator()
    {
        return new \ArrayIterator( $this->getData() );
    }

    //
    //  interface ArrayAccess, modify to permit $object[] = "something" syntax
    //
    function offsetSet( $offset, $value )
    {
        if ( is_null( $offset ) )
            {
                $this->_data[] = $value;
            }
        else
            {
                $this->_data[ $offset ] = $value;
            }
    }

    //
    //  interface Countable
    //
    function count()
    {
        return count( $this->getData() );
    }

}
