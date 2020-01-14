<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Order\Customisation;

class Processing extends \Magento\Framework\View\Element\Html\Select
{

    protected $processing;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Customisation\Processing $processing,
        array $data = []
    )
    {
        $this->processing = $processing;
        parent::__construct( $context, $data  );
    }

    public function setInputName( $value )
    {
        return $this->setName( $value );
    }

    public function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->processing->toOptionArray();
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
