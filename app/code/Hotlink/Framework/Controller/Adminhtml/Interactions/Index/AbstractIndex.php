<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions\Index;

abstract class AbstractIndex extends \Hotlink\Framework\Controller\Adminhtml\Interactions\AbstractInteractions
{

    protected $registry;

    protected $_initPageLayoutCalled = false;
    protected $_initPageLayoutRentry = false;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\Platform\AbstractPlatform $platform,
        \Magento\Framework\Registry $registry
    )
    {
        $this->registry = $registry;
        parent::__construct(
            $context,
            $platform
        );
    }

    function execute()
    {
        $this->_getRegistry()->register( 'current_platform', $this->getPlatform() );
        return $this->getPage();
    }

    function getPageTitle()
    {
        return __( $this->getPlatform()->getName() . " Interactions" );
    }

    protected function _getRegistry()
    {
        return $this->registry;
    }

    function getPage()
    {
        $this->_initPageLayout();
        return $this->_view->getPage();
    }

    protected function _initPageLayout()
    {
        if ( ! $this->_initPageLayoutCalled && ! $this->_initPageLayoutRentry )
            {
                $this->_initPageLayoutRentry = true;
                
                $page = $this->getPage();
                $page->addDefaultHandle();

                $this->_setActiveMenu( $this->getActiveMenuId() );
                $this->_setTitle( $this->getPageTitle() );

                $this->_initPageLayoutCalled = true;
                $this->_initPageLayoutRentry = false;
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
 
}
