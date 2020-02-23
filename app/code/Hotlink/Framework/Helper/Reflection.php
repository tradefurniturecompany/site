<?php
namespace Hotlink\Framework\Helper;

class Reflection
{

    protected static $_config = false;

    public static function parentArgs( $object )
    {
        if ( ! self::$_config )
            {
                self::$_config = \Magento\Framework\App\ObjectManager::getInstance()->get( "\Magento\Framework\ObjectManager\ConfigInterface" );
            }
        $type = get_parent_class( $object );
        $args = self::$_config->getArguments( $type );
        return $args;
    }

    protected $interactionExceptionHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper
    ) {
        $this->interactionExceptionHelper = $interactionExceptionHelper;
    }

    function getModule( $object )
    {
        $class = false;
        if ( is_object( $object ) )
            {
                $class = get_class( $object );
            }
        elseif ( is_string( $object ) )
            {
                $class = $object;
            }
        $module = false;
        if ( $class )
            {
                $parts = explode( '\\', $class );
                if ( $parts[ 0 ] == '' ) array_shift( $parts );
                while ( count( $parts ) > 2 )
                    {
                        array_pop( $parts );
                    }
                $module = implode( '_', $parts );
            }
        return $module;
    }

    // TODO : remove append and leading slash from signature and move to convention - these are not the concern of reflection
    function getClass( $object, $append = null, $leadingSlash = true  )
    {
        if ( is_null ( $object ) )
            {
                return '';
            }
        $class = is_object( $object ) ? '\\' . get_class( $object ) : ( string ) $object;
        if ( !is_null( $append ) )
            {
                if ( is_string( $append ) )
                    {
                        $class = $class . '\\' . $append;
                    }
                elseif ( is_array( $append ) )
                    {
                        array_unshift( $append, $class );
                        $class = implode( '\\', $append );
                    }
            }
        if ( $leadingSlash) {
            if ( substr( $class, 0, 1 ) !== '\\' )
            {
                $class = '\\' . $class;
            }
        }
        else {
            if ( substr( $class, 0, 1 ) == '\\' )
                {
                    $class = substr( $class, 1 );
                }
        }
        return $class;
    }

    function getClassName( $object )
    {
        if ( is_null ( $object ) )
            {
                return '';
            }
        return get_class( $object );
    }

    /*
    function getHelperName( $object )
    {
        $class = get_class( $object );
        $key = 'Helper';
        $key = "_" . $key . "_";
        $parts = explode( $key, $class );
        $module = $this->modeltolower( array_shift( $parts ) );
        $names = $this->modeltolower( implode( $key, $parts ) );
        return $module . '/' . $names;
    }

    function getLastName( $object )
    {
        // NB: PHP cannot resolve explode and array_pop in a single statement (hence two lines of code)
        // Strict Notice: Only variables should be passed by reference
        $name = explode( "_", get_class( $object ) );
        return array_pop( $name );
    }

    //
    //  Returns an xml key for an object
    //
    function getXmlKey( $object )
    {
        $class = get_class( $object );
        $key = strtolower( $class );
        $key = str_replace( '_model_', '', $key );
        return $code;
    }

    //
    //  Provides a descriptive name for a model
    //
    function getName( $object )
    {
        $class = get_class( $object );
        $parts = explode( "_Model_", $class );
        array_shift( $parts );
        $name = array_shift( $parts );
        $name = str_replace( "_", " ", $name );
        return $name;
    }

    function getModelName( $object )
    {
        return $this->extractName( $object, 'Model' );
    }

    function getModelModule( $object, $lowercase = true )
    {
        return $this->extractModule( $object, 'Model', $lowercase );
    }

    //
    //  Calcualtes the alias of an object or classname, optionally returns parts thereof.
    //
    function getAlias( $thing, $modulePart = true, $classPart = true )
    {
        if ( $type = $this->getType( $thing ) )
            {
                if ( is_object( $thing ) )
                    {
                        $thing = get_class( $thing );
                    }
                $type = '_' . $type . '_';
                $parts = explode( $type, $thing );
                $module = $this->modeltolower( array_shift( $parts ) );
                $class = $this->modeltolower( array_pop( $parts ) );
                $result = ( $modulePart ) ? $module : '';
                if ( $classPart )
                    {
                        if ( strlen( $result ) > 0 )
                            {
                                $result .= '/';
                            }
                        $result .= $class;
                    }
                return $result;
            }
        return false;
    }

    function getType( $thing )
    {
        if ( $this->isModel( $thing ) )
            {
                return 'Model';
            }
        if ( $this->isHelper( $thing ) )
            {
                return 'Helper';
            }
        if ( $this->isBlock( $thing ) )
            {
                return 'Block';
            }
        return false;
    }

    //
    //  Naive tests for models, blocks and helpers (does not handle compund names like Company_Module_Helper_Model_Block_Name)
    //
    function isModel( $thing )
    {
        return $this->isType( $thing, 'Model' );
    }

    function isHelper( $thing )
    {
        return $this->isType( $thing, 'Helper' );
    }

    function isBlock( $thing )
    {
        return $this->isType( $thing, 'Block' );
    }

    function isController( $thing )
    {
        $this->interactionExceptionHelper->throwNotImplemented( 'isController is not yet implemented', $this );
    }

    protected function isType( $thing, $type )
    {
        if ( is_object( $thing ) )
            {
                $thing = get_class( $thing );
            }
        if ( is_string( $thing ) )
            {
                $type = '_' . $type . '_';
                return ( strpos( $thing, $type ) !== FALSE );
            }
        return false;
    }

    function getHelperName( $object )
    {
        return $this->extractName( $object, 'Helper' );
    }

    function getHelperModule( $object, $lowercase = true )
    {
        return $this->extractModule( $object, 'Helper', $lowercase );
    }

    function classToModel( $class )
    {
        return $this->convertClassToAlias( $class, "Model" );
    }

    protected function extractModule( $object, $key, $lowercase = true )
    {
        $key = "_" . $key . "_";
        $parts = explode( $key, get_class( $object ) );
        $module = array_shift( $parts );
        if ( $lowercase )
            {
                $module = $this->modeltolower( $module );
            }
        return $module;
    }

    protected function extractName( $object, $key )
    {
        return $this->convertClassToAlias( get_class( $object ), $key );
    }

    protected function convertClassToAlias( $class, $key )
    {
        $key = "_" . $key . "_";
        $parts = explode( $key, $class );
        $module = $this->modeltolower( array_shift( $parts ) );
        $names = $this->modeltolower( implode( $key, $parts ) );
        return $module . '/' . $names;
    }

    protected function modeltolower( $ref )
    {
        $parts = explode( '_', $ref );
        $lower = array();

        if ( false === function_exists('lcfirst') )
            {
                function lcfirst( $str )
                {
                    return (string)(strtolower(substr($str,0,1)).substr($str,1));
                }
            }
        foreach ( $parts as $part )
            {
                $lower[] = lcfirst( $part );
            }
        return implode( '_', $lower );
    }

    // function getHtmlName( $object )
    // {
    //     return strtolower( get_class( $object ) );
    // }

    function requireMethod( $name, $object )
    {
        if ( !method_exists( $object, $name ) )
            {
                $this->interactionExceptionHelper->throwImplementation( 'The method [' . $name . '] is required on class [class]', $object );
            }
        return true;
    }
    */

}
