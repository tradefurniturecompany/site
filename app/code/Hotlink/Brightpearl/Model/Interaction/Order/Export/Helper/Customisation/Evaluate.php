<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation;

class Evaluate extends \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\CustomisationAbstract
{

    function isLiteral( $expression )
    {
        return $this->isEnclosedSquare( $this->clean( $expression ) );
    }

    function getLiteral( $expression )
    {
        $expression = $this->clean( $expression );
        if ( $this->isLiteral( $expression ) )
            {
                return $this->getEnclosed( $expression );
            }
        return '';
    }

    function apply( $csv, $object )
    {
        $expressions = str_getcsv( $csv );
        $failed = [];
        foreach ( $expressions as $expression )
            {
                $result = null;
                try
                    {
                        $result = $this->evaluate( $expression, $object );
                    }
                catch ( \Exception $e )
                    {
                        $failed[] = $e->getMessage();
                    }
                if ( ! is_null( $result ) )
                    {
                        return $result;
                    }
            }
        $failed = implode( "\n", $failed );
        throw new \Exception( "No evaluation result for : $csv \n\n$failed" );
    }

    function evaluate( $expression, $object )
    {
        $expression = $this->clean( $expression );
        if ( $this->isLiteral( $expression ) )
            {
                return $this->getLiteral( $expression );
            }
        if ( $expression )
            {
                $called = [];
                $methods = explode( '.', $expression );
                while ( count( $methods ) > 0 )
                    {
                        $method = array_shift( $methods );
                        if ( ! is_object( $object ) )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "@ " . implode( '.', $called ) . " : not a callable object" ) );
                            }
                        if ( ! is_callable( [ $object, $method ] ) )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "@ " . implode( '.', $called ) . " : $method is not callable" ) );
                            }
                        try
                            {
                                $called[] = $method;
                                $object = $object->$method();
                            }
                        catch ( \Exception $e )
                            {
                                throw new \Hotlink\Brightpearl\Model\Exception\Customisation\Parser( __( "@ " . implode( '.', $called ) . " : " . $e->getMessage() ), $e );
                            }
                    }
            }
        return $object;
    }

}
