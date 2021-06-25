<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Renderer;

class Frame extends \Magento\Framework\View\Element\Template
{
    protected $registry;
    protected $urlBuilder;

    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        array $data = [])
    {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    function toHtml()
    {
        $logId = $this->registry->registry( 'hotlink_framework_report_log_id' );
        $source = $this->urlBuilder->getUrl( 'hotlink_framework/report/render', [ 'id' => $logId ] );
        $html = '<iframe id="report-frame-'.$logId.'" src="'. $source .'" style="display: block; width: 100%; height: 1000px; border: 0px;"></iframe>';

        return $html;
    }
}
