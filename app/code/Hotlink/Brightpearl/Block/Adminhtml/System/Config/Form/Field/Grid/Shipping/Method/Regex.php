<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Shipping\Method;

class Regex extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_mageShippingRenderer;
    protected $_bpShippingRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn( 'magento',
                          [
                              'label'    => __( 'Magento shipping code' )
                          ] );

        $this->addColumn( 'brightpearl',
                          [
                              'label' => __( 'Brightpearl shipping method' ),
                              'renderer' => $this->_getBpShippingRenderer()
                          ] );

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getBpShippingRenderer()
    {
        if ( !$this->_bpShippingRenderer )
            {
                $this->_bpShippingRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Brightpearl\Shipping\Method', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
                $this->_bpShippingRenderer->setExtraParams( 'style="width:250px"' );
            }
        return $this->_bpShippingRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];
        if ( $magento = $row->getData( 'magento' ) )
            {
                //
            }
        if ( $brightpearl = $row->getData( 'brightpearl' ) )
            {
                $options[ 'option_' . $this->_getBpShippingRenderer()->calcOptionHash( $brightpearl ) ] = 'selected="selected"';
            }
        $row->setData( 'option_extra_attrs', $options );
    }

}
