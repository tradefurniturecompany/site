<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions;

abstract class AbstractInteractions extends \Magento\Backend\App\Action
{

    abstract function getActiveMenuId();
    abstract function getActiveMenuResource();
    abstract function getPageTitle();

    protected $platform;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\Platform\AbstractPlatform $platform
    )
    {
        $this->platform = $platform;
        parent::__construct( $context );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed( $this->getActiveMenuResource() );
    }

    function getPlatform()
    {
        return $this->platform;
    }

    protected function _getEventManager()
    {
        return $this->_eventManager; // assigned by parent from $context via constructor
    }

}
