<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Payment;

class Method extends \Magento\Framework\View\Element\Html\Select
{
    protected $paymentMethodSurce;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Magento\Payment\Method\Allowed $paymentMethodSurce,
        array $data = []
    ) {
        $this->paymentMethodSurce = $paymentMethodSurce;
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
                $options = $this->paymentMethodSurce->toArray();
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
