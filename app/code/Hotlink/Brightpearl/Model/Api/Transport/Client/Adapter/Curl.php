<?php
namespace Hotlink\Brightpearl\Model\Api\Transport\Client\Adapter;

class Curl extends \Zend\Http\Client\Adapter\Curl
{

    function write($method, $uri, $httpVersion = 1.1, $headers = array(), $body = '')
    {
        $request = parent::write($method, $uri, $httpVersion, $headers, $body);

        // see:
        // https://github.com/zendframework/zendframework/issues/7683
        // https://github.com/zendframework/zend-http/pull/53
        // https://github.com/zendframework/zend-http/issues/19
        $response = $this->response;
        $response = preg_replace( "/Transfer-Encoding:\s*chunked\\r\\n/i", "", $response );
        $this->response = $response;

        return $request;
    }

}
