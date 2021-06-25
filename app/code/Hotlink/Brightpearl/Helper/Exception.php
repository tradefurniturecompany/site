<?php
namespace Hotlink\Brightpearl\Helper;

class Exception extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BASE = "\Hotlink\Brightpearl\Model\Exception\\";

    /**
     * Creates a new exception of given class and sets its properties, then calls throwException()
     * method to throw it.
     */
    protected function _throw( $type, $args )
    {
        $message = '';
        if ( is_array( $args ) && count( $args ) )
            {
                $text = array_shift( $args );
                $message = __( $text, $args );
            }

        $class = self::BASE . $type;
        $exception = new $class( $message );

        $this->throwException( $exception );
    }

    /**
     * Throws the given exception.
     * Used internally by all of the throw* methods in this class. Should also be used directly
     * by other classes in this module instead of the bare throw, in order to centralize exception
     * handling.
     */
    public function throwException( \Exception $e )
    {
        throw $e;
    }

    /**
     * Creates and throws an API exception.
     * An API exception occurs when the remote API endpoint returns an error which cannot be
     * managed immediately, and which handling should be delegated to the caller, which is
     * supposed to trap and handle this exception, or pass is up the chain (or both).
     * Are examples of API exceptions connectivity errors (connection timeout, host unreachable etc),
     * SOAP fault exceptions (when SOAP is used as transport) etc.
     */
    public function throwApi()
    {
        $this->_throw( 'Api', func_get_args() );
    }

    /**
     * Creates and throws an Authorisation exception.
     */
    public function throwAuthorisation()
    {
        $this->_throw( 'Authorisation', func_get_args() );
    }

    /**
     * Creates and throws a configuration exception.
     * A configuration exception occurs when a misconfiguration error is encountered, or when
     * a functionality executed is disabled at config level.
     */
    public function throwConfiguration()
    {
        $this->_throw( 'Configuration', func_get_args() );
    }

    /**
     * Creates and throws an implementation exception.
     * An implementation exception occurs when module components are not being used as expected,
     * for instance not following the designed patterns, as using an API message class without
     * have set its transaction object first, which is supposed to be done at message instantiation.
     */
    public function throwImplementation()
    {
        $this->_throw( 'Implementation', func_get_args() );
    }

    /**
     * Creates and throws a Transport exception.
     * Are examples of Transport exceptions connectivity errors (connection timeout, host unreachable etc),
     * HTTP Client or CURL adapter exceptions.
     */
    public function throwTransport()
    {
        $this->_throw( 'Transport', func_get_args() );
    }

    /**
     * Creates and throws a validation exception.
     * A validation exception is the one thrown when errors occur during validation process.
     */
    public function throwValidation()
    {
        $this->_throw( 'Validation', func_get_args() );
    }

}
