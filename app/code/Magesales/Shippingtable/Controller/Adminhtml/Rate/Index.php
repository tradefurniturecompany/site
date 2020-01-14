<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Rate;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Rate;

class Index extends Rate
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
		$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$html = $resultPage->getLayout()->createBlock('Magesales\Shippingtable\Block\Adminhtml\Rates')->toHtml();
        $this->getResponse()->setBody($html);
	}
}
