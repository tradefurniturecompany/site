<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Stock;

class Trigger extends \Magento\Framework\View\Element\Html\Select
{

    protected $triggerFactory;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Framework\Model\Trigger\Stock\Update\RealtimeFactory $triggerFactory,
        array $data = []
    )
    {
        $this->triggerFactory = $triggerFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    function setInputName( $value )
    {
        return $this->setName( $value );
    }

    function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $context = $this->triggerFactory->create()->getContexts();
                foreach ( $context as $code => $label )
                    {
                        $code = $this->escapeJsQuote( $code );
                        $label = $this->escapeJsQuote( $label );
                        $this->addOption( $code, $label );
                    }
            }
        return parent::_toHtml();
    }

}
