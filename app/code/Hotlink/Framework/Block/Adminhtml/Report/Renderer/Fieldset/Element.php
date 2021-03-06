<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Renderer\Fieldset;

class Element extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
    }

    function _construct()
    {
        parent::_construct();
        if ( !$this->getTemplate() )
            {
                $this->setTemplate( 'hotlink_framework/report/renderer/fieldset/element.phtml' );
            }
    }

    function render( \Magento\Framework\Data\Form\Element\AbstractElement $element )
    {
        $this->setElement( $element );
        return $this->toHtml();
    }

}
