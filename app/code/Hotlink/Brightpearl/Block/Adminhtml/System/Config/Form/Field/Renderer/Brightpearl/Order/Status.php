<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Brightpearl\Order;

class Status extends \Magento\Framework\View\Element\Html\Select
{
    protected $bpOrderStatusSource;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Status $bpOrderStatusSource,
        array $data = []
    ) {
        $this->bpOrderStatusSource = $bpOrderStatusSource;
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
                $options = $this->bpOrderStatusSource->toArray();
                foreach ($options as $code => $label )
                    { 
                        $code = $this->escapeJsQuote( $code );
                        $label = $this->escapeJsQuote( $label );
                        $this->addOption( $code, $label );
                    }
            }
        return parent::_toHtml();
    }
}
