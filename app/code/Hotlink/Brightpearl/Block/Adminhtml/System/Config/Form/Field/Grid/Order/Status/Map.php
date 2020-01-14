<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Order\Status;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_mageStatusRenderer;
    protected $_bpStatusRenderer;

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn( 'magento', array(
            'label'    => __( 'Magento Status' ),
            'renderer' => $this->_getMageStatusRenderer()
        ));

        $this->addColumn( 'brightpearl', array(
            'label' => __( 'Brightpearl Status' ),
            'renderer' => $this->_getBpStatusRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getMageStatusRenderer()
    {
        if ( !$this->_mageStatusRenderer ) {
            $this->_mageStatusRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Order\Status', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_mageStatusRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_mageStatusRenderer;
    }

    protected function _getBpStatusRenderer()
    {
        if ( !$this->_bpStatusRenderer ) {
            $this->_bpStatusRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Brightpearl\Order\Status', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_bpStatusRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_bpStatusRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject
     */
    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];

        if ( $magento = $row->getData( 'magento' ) ) {
            $options[ 'option_' . $this->_getMageStatusRenderer()->calcOptionHash( $magento )] = 'selected="selected"';
        }

        if ( $brightpearl = $row->getData( 'brightpearl' ) ) {
            $options[ 'option_' . $this->_getBpStatusRenderer()->calcOptionHash( $brightpearl ) ] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
