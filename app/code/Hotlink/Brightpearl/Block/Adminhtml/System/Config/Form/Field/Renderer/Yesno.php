<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer;

class Yesno extends \Magento\Framework\View\Element\Html\Select
{
    protected $yesnoSource;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $yesnoSource,
        array $data = []
    ) {
        $this->yesnoSource = $yesnoSource;
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
                $options = $this->yesnoSource->toArray();
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
