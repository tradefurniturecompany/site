<?php
namespace Hotlink\Brightpearl\Helper\Api\Service\Search;

class Period
{
    const EXACT   = 'exact';
    const BEFORE  = 'before';
    const AFTER   = 'after';
    const BETWEEN = 'between';

    const PERIOD_SEPARATOR = '/';

    /**
     * @var \Hotlink\Brightpearl\Helper\Exception
     */
    protected $brightpearlExceptionHelper;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper
    ) {
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;
    }

    protected function toPeriod($format, \DateTime $date1, \DateTime $date2 = null)
    {
        $period = '';

        switch ($format) {

        case self::EXACT:
            $period = $this->getIsoDate( $date1 );
            break;

        case self::BEFORE:
            $period = self::PERIOD_SEPARATOR . $this->getIsoDate( $date1 );
            break;

        case self::AFTER:
            $period = $this->getIsoDate( $date1 ) . self::PERIOD_SEPARATOR;
            break;

        case self::BETWEEN:
            $period = $this->getIsoDate( $date1 ) . self::PERIOD_SEPARATOR . $this->getIsoDate( $date2 );
            break;

        default:
            $this->brightpearlExceptionHelper->throwImplementation("Invalid period format '".$format."'");
            break;

        }

        return $period;
    }

    protected function getIsoDate( \DateTime $date )
    {
        // http://php.net/manual/en/class.datetime.php
        // Note: This format is not compatible with ISO-8601, but is left this way for backward compatibility reasons. Use DateTime::ATOM or DATE_ATOM for compatibility with ISO-8601 instead.

        return $date->format( \DateTime::ATOM );
    }

    public function toExact(\DateTime $date)
    {
        return $this->toPeriod(self::EXACT, $date);
    }

    public function toBefore(\DateTime $date)
    {
        return $this->toPeriod(self::BEFORE, $date);
    }

    public function toAfter(\DateTime $date)
    {
        return $this->toPeriod(self::AFTER, $date);
    }

    public function toBetween(\DateTime $date1, \DateTime $date2)
    {
        return $this->toPeriod(self::BETWEEN, $date1, $date2);
    }
}
