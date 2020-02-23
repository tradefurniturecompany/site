<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price;

class BreakRenderer extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Tier\Break
     */
    protected $brightpearlConfigSourceBrightpearlPriceTierBreak;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Tier\BreakTier $brightpearlConfigSourceBrightpearlPriceTierBreak,
        array $data = []
    ) {
        $this->brightpearlConfigSourceBrightpearlPriceTierBreak = $brightpearlConfigSourceBrightpearlPriceTierBreak;
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
                $options = $this->brightpearlConfigSourceBrightpearlPriceTierBreak->toOptionArray();
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