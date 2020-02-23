<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions\Execute;

abstract class AbstractExecute extends \Hotlink\Framework\Controller\Adminhtml\Interactions\AbstractInteractions
{

    protected $responseFactory;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\Platform\AbstractPlatform $platform,
        \Hotlink\Framework\Controller\Adminhtml\ResponseFactory $responseFactory
    )
    {
        $this->responseFactory = $responseFactory;
        parent::__construct(
            $context,
            $platform
        );
    }

    function execute()
    {
        $data = $this->getRequest();
        $this->_getEventManager()->dispatch( "hotlink_framework_trigger_admin_user_request", array( 'request' => $data ) );
        $result = $this->_getResponseFactory()->create();
        return $result;
    }

    function getPageTitle()
    {
        return __( $this->getPlatform()->getName() . " Executing" );
    }

    protected function _getResponseFactory()
    {
        return $this->responseFactory;
    }

}
