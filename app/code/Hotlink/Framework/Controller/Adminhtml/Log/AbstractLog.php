<?php
namespace Hotlink\Framework\Controller\Adminhtml\Log;

abstract class AbstractLog extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Hotlink_Framework::log';    // used by _isAllowed
    const ACTIVE_MENU = 'Hotlink_Framework::log';

    protected $_initPageLayoutCalled = false;

    protected $registry;
    protected $reportHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,

        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Helper\Report $reportHelper
    )
    {
        $this->registry = $registry;
        $this->reportHelper = $reportHelper;
        parent::__construct( $context );
    }

    protected function _initPageLayout()
    {
        if ( ! $this->_initPageLayoutCalled )
            {
                $page = $this->getPage();
                $page->addDefaultHandle();
                $this->_initPageLayoutCalled = true;
            }
        return $this;
    }

    protected function _setActiveMenu( $itemId )
    {
        $this->_initPageLayout();
        $menuBlock = $this->_view->getLayout()->getBlock( 'menu' );
        $menuBlock->setActive( $itemId );
        return $this;
    }

    protected function _setTitle( $name )
    {
        $this->_initPageLayout();
        $this->getPage()->getConfig()->getTitle()->set( $name );
        return $this;
    }

    public function getPage()
    {
        return $this->_view->getPage();
    }

}
