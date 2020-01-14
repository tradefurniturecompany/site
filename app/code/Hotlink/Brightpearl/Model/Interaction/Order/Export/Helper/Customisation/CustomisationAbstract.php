<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation;

class CustomisationAbstract
{

    public function clean( $string )
    {
        return trim( $string );
    }

    public function isEnclosed( $leftCharacter, $string, $rightCharacter )
    {
        $result = ( ( substr( $string, 0, 1 ) == $leftCharacter ) && ( substr( $string, -1, 1 ) == $rightCharacter ) );
        return $result;
    }

    public function isEnclosedSquare( $string )
    {
        return $this->isEnclosed( '[', $string, ']' );
    }

    public function isEnclosedCurly( $string )
    {
        return $this->isEnclosed( '{', $string, '}' );
    }

    public function getEnclosed( $string )
    {
        $string = substr( $string, 1 );
        $string = substr( $string, 0, strlen( $string ) - 1 );
        return $string;
    }

}
