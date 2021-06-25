<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer;

class Website extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Source\Magento\Website
     */
    protected $brightpearlConfigSourceMagentoWebsite;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Brightpearl\Model\Config\Source\Magento\Website $brightpearlConfigSourceMagentoWebsite,
        array $data = []
    ) {
        $this->brightpearlConfigSourceMagentoWebsite = $brightpearlConfigSourceMagentoWebsite;
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
                $options = $this->brightpearlConfigSourceMagentoWebsite->toOptionArray();
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
