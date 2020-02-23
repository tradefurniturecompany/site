<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Order;

class Status extends \Magento\Framework\View\Element\Html\Select
{
    protected $mageOrderStatusSource;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Framework\Model\Config\Field\Order\Status\Source $mageOrderStatusSource,
        array $data = []
    ) {
        $this->mageOrderStatusSource = $mageOrderStatusSource;
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
                $options = $this->mageOrderStatusSource->toOptionArray();
                foreach ( $options as $_opt )
                    {
                        $value = $this->escapeJsQuote( $_opt[ 'value' ] );
                        $label = $this->escapeJsQuote( $_opt[ 'label' ] );
                        $this->addOption( $value, $label );
                    }
            }
        return parent::_toHtml();
    }

}
