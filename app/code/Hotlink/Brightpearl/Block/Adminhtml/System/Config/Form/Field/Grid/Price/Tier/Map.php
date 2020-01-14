<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Price\Tier;

class Map extends \Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Price\Group\Map
{
    protected $_breakRenderer;

    protected function _prepareToRender()
    {
        parent::_prepareToRender();

        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_BREAK,
                          [ 'label' => __( 'Break' ),
                            'style' => 'width:150px',
                            'renderer' => $this->_getBreakRenderer() ] );
    }

    protected function _getBreakRenderer()
    {
        if ( !$this->_breakRenderer ) {
            $this->_breakRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price\BreakRenderer', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_breakRenderer->setExtraParams( 'style="width:150px"' );
        }
        return $this->_breakRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        parent::_prepareArrayRow( $row );

        $options = $row->getData( 'option_extra_attrs' );

        $break = $row->getData( \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_BREAK );
        $options[ 'option_' . $this->_getBreakRenderer()->calcOptionHash( $break) ] = 'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }
}