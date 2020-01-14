<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price;

class Behaviour extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Missing\Behaviour
     */
    protected $brightpearlConfigSourceBrightpearlPriceMissingBehaviour;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Missing\Behaviour $brightpearlConfigSourceBrightpearlPriceMissingBehaviour,
        array $data = []
    ) {
        $this->brightpearlConfigSourceBrightpearlPriceMissingBehaviour = $brightpearlConfigSourceBrightpearlPriceMissingBehaviour;
        parent::__construct(
            $context,
            $data
        );
    }

    public function setInputName( $value )
    {
        return $this->setName( $value );
    }

    public function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->brightpearlConfigSourceBrightpearlPriceMissingBehaviour->toOptionArray();
                foreach ( $options as $item )
                    {
                        $value = $this->escapeJsQuote( $item[ 'value' ] );
                        $label = $this->escapeJsQuote( $item[ 'label' ] );
                        $this->addOption( $value, $label );
                    }
            }
        return parent::_toHtml();
    }
}
