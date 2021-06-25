<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Log\Form\Button;

class Back extends \Hotlink\Framework\Block\Adminhtml\Report\Log\Form\Button  implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{

    function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
