<?php
namespace Hotlink\Framework\Controller\Adminhtml\Report;

class Render extends \Magento\Backend\App\Action
{

    protected $interactionReportFactory;
    protected $httpResponseFactory;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Controller\Adminhtml\ResponseFactory $httpResponseFactory
    )
    {
        $this->interactionReportFactory = $interactionReportFactory;
        $this->httpResponseFactory = $httpResponseFactory;

        parent::__construct( $context );
    }

    function execute()
    {
        $response = $this->httpResponseFactory->create();

        $logId = (int) $this->getRequest()->getParam('id');

        if ( $logId )
            {
                $report = $this->interactionReportFactory->create( ['object' => $this]  )->load( $logId );
                $report->addHtmlWriter();
                $report->setUseDataReader();
                while ( $item = $report->read() )
                    {
                        $report->write( $item );
                    }
                $report->close();
            }
        else
            {
                $response->add(  __( 'Required parameter "id" missing.' ) );
            }

        return $response;
    }

}
