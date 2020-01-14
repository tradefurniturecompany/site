<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Log\Form;

class Button
{
    protected $reportLogFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hotlink\Framework\Model\Report\LogFactory $reportLogFactory
    ) {
        $this->context = $context;
        $this->reportLogFactory = $reportLogFactory;
    }

    protected function getReportLogId()
    {
        $log = $this->reportLogFactory->create()->load(
            $this->context->getRequest()->getParam( 'id' )
            );

        return ( $log->getRecordId() )
            ? $log->getRecordId()
            : null;
    }

    public function getUrl( $route = '', $params = [] )
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
