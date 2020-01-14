<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Shipping;

class Method extends \Magento\Framework\View\Element\Html\Select
{
    protected $source;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Magento\Shipping\Method\Allowed $source,
        array $data = []
    ) {
        $this->source = $source;
        parent::__construct( $context, $data  );
    }

    public function setInputName( $value )
    {
        return $this->setName( $value );
    }

    public function escape( $option )
    {
        $value = $option[ 'value' ];
        $label = $option[ 'label' ];
        if ( is_array( $value ) )
            {
                $result = [];
                foreach ( $value as $opt )
                    {
                        $result[] = $this->escape( $opt );
                    }
                $value = $result;
            }
        else
            {
                $value = $this->escapeJsQuote( $value );
            }
        $option[ 'value' ] = $value;
        $option[ 'label' ] = $this->escapeJsQuote( $label );
        return $option;
    }

    public function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->source->toOptionArray();
                foreach ( $options as $_option )
                    {
                        $option = $this->escape( $_option );
                        $value = $option[ 'value' ];
                        $label = $option[ 'label' ];
                        $this->addOption( $value, $label );
                    }
            }
        return parent::_toHtml();
    }

}
