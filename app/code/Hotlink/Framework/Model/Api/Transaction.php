<?php
namespace Hotlink\Framework\Model\Api;

class Transaction
{

    //
    //  To force a particular request or response object to be used, provide the methods:
    //
    //       protected function _getRequest()
    //       protected function _getResponse()
    //
    //  in derived classes. If these functions are available, their returned objects will be used. If not, convention is used to retrieve
    //  the models.
    //
    //  Note that once you provide these functions in your class, convention will cease to be applied in derived classes (since
    //  these functions will be used), unless you redeclare these in derived classes to return false, in which case convention will
    //  apply until a valid object is returned.
    //
    //  The algorithm purposely does not use is_callable, and so delcaring these function as private is not supported.
    //
    //  The convention is:
    //    1. Check if an "_request" or "_reponse" model exists for the given transaction class name, and use if found
    //    2. Obtain the parent class name, and repeat 1
    //
    //  If no corresponding _request or _response models exists, the algorithm will terminate when this class is reached
    //  (i.e. Hotlink_Framework_Model_Api_Transaction), as it does provide these models.
    //

    protected $_request = false;
    protected $_response = false;

    //
    //  Provide a useful default name for the transaction (overload if required)
    //


    /**
     * @var \Hotlink\Framework\Helper\Reflection
     */
    protected $interactionReflectionHelper;

    /**
     * @var \Hotlink\Framework\Model\Api\RequestFactory
     */
    protected $interactionApiRequestFactory;

    /**
     * @var \Hotlink\Framework\Helper\Exception
     */
    protected $interactionExceptionHelper;

    /**
     * @var \Hotlink\Framework\Model\Api\ResponseFactory
     */
    protected $interactionApiResponseFactory;

    //
    public function __construct(
        \Hotlink\Framework\Helper\Reflection $interactionReflectionHelper,
        \Hotlink\Framework\Model\Api\RequestFactory $interactionApiRequestFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Model\Api\ResponseFactory $interactionApiResponseFactory
    ) {
        $this->interactionReflectionHelper = $interactionReflectionHelper;
        $this->interactionApiRequestFactory = $interactionApiRequestFactory;
        $this->interactionExceptionHelper = $interactionExceptionHelper;
        $this->interactionApiResponseFactory = $interactionApiResponseFactory;
    }
        public function getName()
    {
        return $this->interactionReflectionHelper ->getName( $this );
    }

    //
    //  PHP Failure
    //  $this->setRequest( parent::getRequest() );
    //  Using parent:: generates Fatal error: Cannot access parent:: when current class scope has no parent
    //  Therefore this algorithm cannot be written recursively, hence the obscure loop
    //
    public function getRequest()
    {
        if ( !$this->_request )
            {
                if ( method_exists( $this, '_getRequest' ) )
                    {
                        $object = $this->_getRequest();
                        if ( $object instanceof \Hotlink\Framework\Model\Api\Request )
                            {
                                $this->setRequest( $object );
                            }
                    }
                if ( !$this->_request )
                    {
                        //$request = Mage::getModel('hotlink_framework/api_request');
                        $request = $this->interactionApiRequestFactory->create();
                        $this->setRequest( $request );
                    }
            }
        return $this->_request;
    }

    public function setRequest( \Hotlink\Framework\Model\Api\Request $request )
    {
        if ( $this->_response )
            {
                $this->interactionExceptionHelper->throwProcessing( 'Request object cannot be changed after response has been set in [class]', $this );
            }
        $request->setTransaction( $this );
        $this->_request = $request;
        return $this;
    }

    public function getResponse()
    {
        if ( !$this->_response )
            {
                if ( method_exists( $this, '_getResponse' ) )
                    {
                        $object = $this->_getResponse();
                        if ( $object instanceof \Hotlink\Framework\Model\Api\Response )
                            {
                                $this->setResponse( $object );
                            }
                    }
                if ( !$this->_response )
                    {
                        $response = $this->interactionApiResponseFactory->create();
                        $this->setResponse( $response );
                    }
            }
        return $this->_response;
    }

    public function setResponse( \Hotlink\Framework\Model\Api\Response $response )
    {
        if ( $this->_response )
            {
                $this->interactionExceptionHelper->throwProcessing( 'Response has already been set in [class]', $this );
            }
        $response->setTransaction( $this );
        $this->_response = $response;
        return $this;
    }

}
