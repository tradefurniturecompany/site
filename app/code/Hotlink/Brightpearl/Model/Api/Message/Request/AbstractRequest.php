<?php
namespace Hotlink\Brightpearl\Model\Api\Message\Request;

abstract class AbstractRequest extends \Hotlink\Framework\Model\Api\Request implements \Hotlink\Brightpearl\Model\Api\Message\Request\IRequest
{
    const ENCODING_JSON = 'json';
    const ENCODING_URLENCODED = 'urlencoded';
    const DATE_FORMAT = \Zend_Date::ISO_8601;

    /**
     * @var \Hotlink\Brightpearl\Helper\Exception
     */
    protected $brightpearlExceptionHelper;

    protected $jsonHelper;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;
    }

    public function jsonHelper()
    {
        return $this->jsonHelper;
    }

    protected function _assertNotEmpty($value, $name)
    {
        if (!\Zend_Validate::is($value,'NotEmpty')) {
            $this->getExceptionHelper()->throwValidation("Empty value for '$name'");
        }
        return $this;
    }

    protected function _assertType($thing, $type, $name)
    {
        if ($type !== gettype($thing)) {
            $this->getExceptionHelper()->throwValidation("Invalid type [".gettype($thing)."] given for '".$name."'. Expected [$type].");
        }
        return $this;
    }

    protected function _assertNumeric($value, $name)
    {
        if (!is_numeric($value)) {
            $this->getExceptionHelper()->throwValidation("Invalid type [".gettype($thing)."] given for '".$name."'. Expected 'numeric'");
        }
        return $this;
    }

    protected function _assertDatetime($value, $name)
    {
        $validator = new \Zend_Validate_Date( array('format' => self::DATE_FORMAT) );
        if (!$validator->isValid($value)) {
            $this->getExceptionHelper()->throwValidation("Invalid datetime value for field '$name'");
        }
        return $this;
    }

    protected function _csv($array)
    {
        return is_array($array) ? implode(',', $array) : $array;
    }

    public function getExceptionHelper()
    {
        return $this->brightpearlExceptionHelper;
    }

    protected function _encodeJson($array)
    {
        return $this->jsonHelper()->jsonEncode( $array );
    }

    protected function _encodeUrlencoded( $fields )
    {
        $params = [];
        foreach ( $fields as $k => $v )
            {
                $pk = urlencode( $k );
                $pv = urlencode( $v );
                $params[] = $pk . "=" . $pv;
            }
        $result = implode( '&', $params );
        return $result;
    }

    /**
     * Encodes $param according to BP API.
     */
    public static function encodeParam($param)
    {
        return rawurlencode($param);
    }

    protected static function _chunk($limit, array $params, array $info, $formatFn = null, $margin = 5)
    {
        $chunks = $rejected = array();

        $format = ($formatFn == null) ? array('self', 'formatParam') : $formatFn;

        $staticParams = $info['static-params'];
        $lenOfStatic = 0;
        foreach ($staticParams as $pname) {
            $lenOfStatic += strlen( $pname.'='. self::encodeParam( call_user_func($format, $params[ $pname ])));
        }

        $chunkLimit = $limit - $lenOfStatic - $margin - (count($staticParams)); // 1 for each & character

        if ($chunkLimit <= 0)
            return array(null, null);

        $chunkName = $info['chunk-param'];
        $bits      = $params[ $chunkName ];

        $accumulator = array();
        foreach ($bits as $bit) {

            if (strlen($bit) >= $chunkLimit) {
                // bit too big to fit, even on its own
                $rejected[] = $bit;
                continue;
            }

            $accumulator[]  = $bit;
            $accumulatorLen = strlen($chunkName . '=' . self::encodeParam( call_user_func($format, $accumulator) ));

            if ($accumulatorLen > $chunkLimit) {

                $last = array_pop($accumulator); // keep the overflow bit

                $chunks[] = $accumulator;

                // reset
                $accumulator = array();

                // carry over the last bit
                $accumulator[] = $last;
            }
        }

        if ($accumulator)
            $chunks[] = $accumulator;

        return array($chunks, $rejected);
    }

    protected static function formatParam(array $param)
    {
        return implode(",", $param);
    }
}