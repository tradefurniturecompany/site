<?php
namespace Hotlink\Brightpearl\Model\Api\Message\Response;

abstract class AbstractResponse extends \Hotlink\Framework\Model\Api\Response
{
    const CODE_NOT_FOUND = 404;

    protected $_content;
    protected $_headers;

    /**
     * @var \Hotlink\Brightpearl\Helper\Exception
     */
    protected $brightpearlExceptionHelper;

    protected $jsonHelper;

    function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;
    }
    function validate()
    {
        if (is_array($this->_content) && array_key_exists('errors', $this->_content)) {
            $this->getExceptionHelper()->throwValidation($this->getErrorMessage());
        }
        return $this->_validate();
    }

    // override this
    protected function _validate()
    {
        return $this;
    }

    function setContent($content)
    {
        if (is_array($content)) {
            $this->_content = $content;
        }
        else {
            try {
                $decoded = $this->jsonHelper->jsonDecode($content, \Zend_Json::TYPE_ARRAY);
                $this->_content = $decoded ? $decoded : null;
            }
            catch ( \Zend_Json_Exception $e ) {
                $this->_content = null;
            }
        }
        return $this;
    }

    function setHeaders( \Zend\Http\Headers $headers )
    {
        $this->_headers = $headers;
        return $this;
    }

    function getHeader( $name )
    {
        return $this->_headers->has( $name )
            ? $this->_headers->get( $name )
            : null;
    }

    protected function getErrorMessage()
    {
        $msg = '';
        if (is_array($this->_content) && array_key_exists('errors', $this->_content)) {
            foreach ((array) $this->_content['errors'] as $error) {
                if (isset($error['code']))
                    $msg .= $error['code'] .' ';
                if (isset($error['message']))
                    $msg .= $error['message'];
            }
        }
        return $msg;
    }

    /**
     * @param array|string $key key to lookup. array for nested levels.
     * i.e.   'response'
     *        array('response', 'apiDomain')
     */
    protected function _get($key)
    {
        return $this->_getIfkeyExists($key, $this->_content);
    }

    protected function _getIfkeyExists($key, $data)
    {
        if (is_array($key)) {
            if (!count($key)) return $data;

            $current = array_shift($key);
            if (array_key_exists($current, $data))
                // continue the recursion
                return $this->_getIfkeyExists($key, $data[$current]);
            else
                return null;
        }
        return array_key_exists($key, $data) ? $data[$key] : null;
    }

    function getExceptionHelper()
    {
        return $this->brightpearlExceptionHelper;
    }
}