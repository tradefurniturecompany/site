<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation;

class Transform extends \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\CustomisationAbstract
{

    public function isDateFormat( $expression )
    {
        return $this->isEnclosedSquare( $this->clean( $expression ) );
    }

    public function getDateFormat( $expression )
    {
        $expression = $this->clean( $expression );
        if ( $this->isDateFormat( $expression ) )
            {
                return $this->getEnclosed( $expression );
            }
        return '';
    }

    public function isLookup( $expression )
    {
        return $this->isEnclosedCurly( $this->clean( $expression ) );
    }

    public function getLookup( $expression )
    {
        $expression = $this->clean( $expression );
        if ( $this->isLookup( $expression ) )
            {
                $lookup = $this->getEnclosed( $expression );
                $items = str_getcsv( $lookup );
                $result = [];
                foreach ( $items as $item )
                    {
                        $parts = explode( '=', $item );
                        if ( count( $parts ) != 2 )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "Unable to create list item from string '$item'" ) );
                            }
                        $key = $this->clean( $parts[ 0 ] );
                        $val = $this->clean( $parts[ 1 ] );
                        $result[ $key ] = $val;
                    }
                return $result;
            }
        return $this->isEnclosedCurly( $expression );
    }

    public function apply( $expression, $value )
    {
        if ( $expression = $this->clean( $expression ) )
            {
                if ( $this->isDateFormat( $expression ) )
                    {
                        $format = $this->getDateFormat( $expression );
                        if ( ! is_int( $value ) )
                            {
                                $value = strtotime( $value );
                            }
                        $value = date( $format, $value );
                    }
                else if ( $this->isLookup( $expression ) )
                    {
                        if ( is_object( $value ) )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "Cannot perform lookup when value is an object" ) );
                            }
                        $lookup = $this->getLookup( $expression );
                        if ( ! array_key_exists( $value, $lookup ) )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "No lookup key defined for '$value' " ) );
                            }
                        $value = $lookup[ $value ];
                    }
                else
                    {
                        // use sprintf
                        try
                            {
                                $value = sprintf( $expression, $value );
                            }
                        catch ( \Exception $e )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( $e->getMessage() ), $e );
                            }
                    }
            }
        return $value;
    }

}
