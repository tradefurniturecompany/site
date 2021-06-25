<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation;

class CustomisationAbstract
{

    function clean( $string )
    {
        return trim( $string );
    }

    function isEnclosed( $leftCharacter, $string, $rightCharacter )
    {
        $result = ( ( substr( $string, 0, 1 ) == $leftCharacter ) && ( substr( $string, -1, 1 ) == $rightCharacter ) );
        return $result;
    }

    function isEnclosedSquare( $string )
    {
        return $this->isEnclosed( '[', $string, ']' );
    }

    function isEnclosedCurly( $string )
    {
        return $this->isEnclosed( '{', $string, '}' );
    }

    function getEnclosed( $string )
    {
        $string = substr( $string, 1 );
        $string = substr( $string, 0, strlen( $string ) - 1 );
        return $string;
    }

}
