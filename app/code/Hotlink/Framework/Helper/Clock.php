<?php
namespace Hotlink\Framework\Helper;

class Clock
{

    //
    //  returns the current timestamp in millionths of a second
    //
    public function microtime_float()
    {
        list( $usec, $sec ) = explode( " ", microtime() );
        return ( ( float ) $usec + ( float ) $sec );
    }

}
