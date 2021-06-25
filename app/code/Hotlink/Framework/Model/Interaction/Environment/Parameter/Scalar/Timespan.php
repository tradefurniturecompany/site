<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar;

class Timespan extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar\AbstractScalar
{

    function getName()
    {
        return 'Timespan';
    }

    function getKey()
    {
        return 'timespan';
    }

    function getOptions()
    {
        return array( 'seconds'  => 'Seconds',
                      'minutes'  => 'Minutes',
                      'hours'    => 'Hours',
                      'days'     => 'Days',
                      'weeks'    => 'Weeks',
                      'months'   => 'Months',
                      'years'    => 'Years' );
    }

    function getSeconds()
    {
        $seconds = 0;
        $value = $this->getValue();
        $seconds = strtotime( $value, 0 );

        /*
        $unit = $this->getUnit();
        $duration = "$value $unit";
        $seconds = strtotime( $duration, 0 );
        */
        return $seconds;
    }

    function getDate( $future = false )
    {
        $seconds = $this->getSeconds();

        $time = ( $future )
            ? time() + $seconds
            : time() - $seconds;

        $date = date( 'Y-m-d H:i:s', $time );
        return $date;
    }

    function asString()
    {
        $date = $this->getDate();
        $output = parent::asString() . " ($date)";
        return $output;
    }

}
