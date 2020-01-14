<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Element;

class Action extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate( 'system/config/form/element/action.phtml' );
        }
        return $this;
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __( $originalData['button_label'] ),
                'html_id'      => $element->getHtmlId(),
                'action_url'   => $this->_urlBuilder->getUrl( $originalData['button_url'] ),
            ]
        );

        return $this->_toHtml();
    }
}
