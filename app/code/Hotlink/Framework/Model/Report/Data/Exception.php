<?php
namespace Hotlink\Framework\Model\Report\Data;

class Exception extends \Exception implements \Serializable, \Hotlink\Framework\Model\Report\IReportData
{

    const OBJECT_PREFIX = '=obj=';  // This need to be regex friendly (ie. contain no special regex chars)

    protected $_previousTrace = false;
    protected $_safetrace = false;

    function __construct( $message, $code = 0, \Exception $previous = null )
    {
        if ( $previous )
            {
                $this->_previousTrace = $previous->getTrace();
            }

        parent::__construct(
            $message,
            $code,
            $previous);
    }

    function getBestTrace()
    {
        if ( $this->_previousTrace )
            {
                return $this->_previousTrace;
            }
        return parent::getTrace();
    }

    function serialize()
    {
        $data = array( 'message' => $this->message,
                       'code'    => $this->code,
                       'file'    => $this->getFile(),
                       'line'    => $this->getLine(),
                       'trace'   => $this->getSafeTrace() );
        return serialize( $data );
    }

    function unserialize( $serialized )
    {
        $data = unserialize( $serialized );
        $this->message = $data[ 'message' ];
        $this->code = $data[ 'code' ];
        $this->file = $data[ 'file' ];
        $this->line = $data[ 'line' ];
        $this->_safetrace = $data[ 'trace' ];
    }

    function getReportDataRenderer()
    {
        return '\Hotlink\Framework\Block\Adminhtml\Report\Item\Data\Exception';
    }

    function getSafeTrace()
    {
        if ( ! $this->_safetrace )
            {
                $this->_safetrace = $this->_trace( $this->getBestTrace() );
            }
        return $this->_safetrace;
    }

    function argsToString( $items )
    {
        $compose = array();
        foreach ( $items as $key => $item )
            {
                $value = false;
                if ( is_array( $item ) )
                    {
                        $compose[] = '[' . $this->argsToString( $item ) . ']';
                    }
                else
                    {
                        if ( $this->_startsWith( $item, self::OBJECT_PREFIX ) )
                            {
                                $text = "$" . str_replace( self::OBJECT_PREFIX, '', $item );
                            }
                        else
                            {
                                $text = htmlspecialchars( $item );
                                $text = ( strlen( $text ) > 200 )
                                      ? "'" . substr( $text, 0, 180 ) . "... &lt;TRUNCATED&gt; ...'"
                                      : "'$item'";
                            }
                        $compose[] = ( $key && !is_numeric( $key ) )
                            ? $key . ' => ' . $text
                            : $text;
                    }
            }
        $result = implode( ', ', $compose );
        return $result;
    }

    protected function _trace( $items )
    {
        $newItems = array();
        foreach ( $items as $key => $item )
            {
                $value = false;
                if ( is_object ( $item ) )
                    {
                        $value = self::OBJECT_PREFIX . get_class( $item );
                    }
                else if ( is_array( $item ) )
                    {
                        $value = $this->_trace( $item );
                    }
                else
                    {
                        $value = ( string ) $item;
                    }
                $newItems[ $key ] = $value;
            }
        return $newItems;
    }

    //
    // http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
    //
    protected function _startsWith( $haystack, $needle )
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== FALSE;
    }

}
