<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\OAuth2;

abstract class AbstractOAuth2 extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Hotlink_Brightpearl::oauth2';

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct( $context );
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu( 'Hotlink_Brightpearl::oauth2' );
        $resultPage->getConfig()->getTitle()->prepend( ( __( 'Brightpearl OAuth2' ) ) );
        return $resultPage;
    }

}
