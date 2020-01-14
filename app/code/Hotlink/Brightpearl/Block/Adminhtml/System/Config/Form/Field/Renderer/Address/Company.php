<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Address;

class Company extends \Magento\Framework\View\Element\Html\Select
{
    protected $source;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Customer\Company $source,
        array $data = []
    ) {
        $this->source = $source;
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
                $options = $this->source->toArray();
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
