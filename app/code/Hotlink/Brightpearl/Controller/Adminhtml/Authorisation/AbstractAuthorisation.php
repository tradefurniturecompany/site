<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Authorisation;

abstract class AbstractAuthorisation extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Hotlink_Brightpearl::authorisation';

    protected $resultRedirectFactory;
    protected $urlBuilder;
    protected $interactionReportFactory;
    protected $interactionUserFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Model\UserFactory $interactionUserFactory
    )
    {
        $this->interactionReportFactory = $interactionReportFactory;
        $this->interactionUserFactory = $interactionUserFactory;

        parent::__construct($context);
        $this->urlBuilder = $this->_backendUrl;
        $this->resultRedirectFactory = $this->resultRedirectFactory;
    }

    protected function _initReport()
    {
        $report = $this->interactionReportFactory->create( [ 'object' => $this ] );
        $report
            ->setUser( $this->interactionUserFactory->create()->getDescription() )
            ->addLogWriter()
            ->addItemWriter()
            ->addDataWriter();
        return $report;
    }

    protected function redirect( $url, $build = true )
    {
        if ($build) {
            $url = $this->urlBuilder->getUrl($url);
        }

        return $this->resultRedirectFactory->create()->setUrl( $url );
    }
}
