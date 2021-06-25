<?php
namespace Hotlink\Brightpearl\Model\Api\Transaction;

abstract class AbstractTransaction extends \Hotlink\Framework\Model\Api\Transaction
{

    abstract protected function _getRequestModel();
    abstract protected function _getResponseModel();

    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Reflection $interactionReflectionHelper,
        \Hotlink\Framework\Model\Api\RequestFactory $interactionApiRequestFactory,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Model\Api\ResponseFactory $interactionApiResponseFactory
    )
    {
        parent::__construct(
            $interactionReflectionHelper,
            $interactionApiRequestFactory,
            $interactionExceptionHelper,
            $interactionApiResponseFactory );
        $this->factoryHelper = $factoryHelper;
    }

    public function _getRequest()
    {
        return $this->factoryHelper->create( $this->_getRequestModel() );
    }

    public function _getResponse()
    {
        return $this->factoryHelper->create( $this->_getResponseModel() );
    }

}