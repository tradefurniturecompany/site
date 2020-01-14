<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Item;

class Data extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    public function _construct()
    {
        $this->setTemplate( 'Hotlink_Framework::report/item/data.phtml' );
    }

    public function render( \Magento\Framework\Data\Form\Element\AbstractElement $element )
    {
        $this->setElement( $element );
        return $this->toHtml();
    }

    public function getReportDataChildHtml()
    {
        $data = $this->getReportData();
        if ( $renderer = $data->getRenderer() )
            {
                $block = false;
                try
                    {
                        $block = $this->_layout->getBlockSingleton( $renderer );
                    }
                catch ( \Exception $e )
                    {
                    }
                if ( $block )
                    {
                        $block->setReportData( $data );
                        return $block->toHtml();
                    }
            }
        return false;
    }
}
