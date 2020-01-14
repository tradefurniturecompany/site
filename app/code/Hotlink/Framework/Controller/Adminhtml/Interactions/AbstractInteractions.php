<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions;

abstract class AbstractInteractions extends \Magento\Backend\App\Action
{

    abstract public function getActiveMenuId();
    abstract public function getActiveMenuResource();
    abstract public function getPageTitle();

    protected $platform;

    public function __construct(
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

    public function getPlatform()
    {
        return $this->platform;
    }

    protected function _getEventManager()
    {
        return $this->_eventManager; // assigned by parent from $context via constructor
    }

}
