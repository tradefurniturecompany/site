<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price;

class ListPrice extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists
     */
    protected $brightpearlConfigSourceBrightpearlPriceLists;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists $brightpearlConfigSourceBrightpearlPriceLists,
        array $data = []
    ) {
        $this->brightpearlConfigSourceBrightpearlPriceLists = $brightpearlConfigSourceBrightpearlPriceLists;
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
                $options = $this->brightpearlConfigSourceBrightpearlPriceLists->toOptionArray();
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