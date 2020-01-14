<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar;

class Timespan extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scalar\AbstractScalar
{

    public function getName()
    {
        return 'Timespan';
    }

    public function getKey()
    {
        return 'timespan';
    }

    public function getOptions()
    {
        return array( 'seconds'  => 'Seconds',
                      'minutes'  => 'Minutes',
                      'hours'    => 'Hours',
                      'days'     => 'Days',
                      'weeks'    => 'Weeks',
                      'months'   => 'Months',
                      'years'    => 'Years' );
    }

    public function getSeconds()
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

    public function getDate( $future = false )
    {
        $seconds = $this->getSeconds();

        $time = ( $future )
            ? time() + $seconds
            : time() - $seconds;

        $date = date( 'Y-m-d H:i:s', $time );
        return $date;
    }

    public function asString()
    {
        $date = $this->getDate();
        $output = parent::asString() . " ($date)";
        return $output;
    }

}
