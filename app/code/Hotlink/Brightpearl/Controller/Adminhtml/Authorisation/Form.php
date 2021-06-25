<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Authorisation;

class Form extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Hotlink_Brightpearl::authorisation';

    protected $resultPageFactory;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct( $context );
    }

    function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu( 'Hotlink_Brightpearl::authorisation' );
        $resultPage->getConfig()->getTitle()->prepend( ( __( 'Brightpearl Authorisation' )) );

        return $resultPage;
    }

}
