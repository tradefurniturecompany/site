<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Brightpearl\Shipping;

class Method extends \Magento\Framework\View\Element\Html\Select
{
    protected $source;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Shipping\Method $source,
        array $data = []
    ) {
        $this->source = $source;
        parent::__construct( $context, $data  );
    }

    function setInputName( $value )
    {
        return $this->setName( $value );
    }

    function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->source->toArray();
                foreach ( $options as $code => $label )
                    {
                        $code = $this->escapeJsQuote( $code );
                        $label = $this->escapeJsQuote( $label );  // TODO: escape nested elements
                        $this->addOption( $code, $label );
                    }
            }
        return parent::_toHtml();
    }

}
