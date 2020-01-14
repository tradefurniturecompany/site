<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Order\Customisation;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_processingRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn( 'evaluate', [
            'label' => __( 'Evaluate' ),
            'style' => 'width:250px;'
        ] );
        $this->addColumn( 'transform', [
            'label'    => __( 'Transform' ),
            'style' => 'width:250px;'
        ] );
        $this->addColumn( 'output', [
            'label' => __( 'Output' ),
            'style' => 'width:250px;'
        ] );
        $this->addColumn( 'processing', [
            'label'    => __( 'On Failure' ),
            'renderer' => $this->_getProcessingRenderer()
        ] );
        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getProcessingRenderer()
    {
        if ( !$this->_processingRenderer )
            {
                $this->_processingRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Order\Customisation\Processing', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
                $this->_processingRenderer->setExtraParams( 'style="width:150px"' );
            }
        return $this->_processingRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];
        if ( $source = $row->getData( 'evaluate' ) )
            {
            }
        if ( $target = $row->getData( 'transform' ) )
            {
            }
        if ( $format = $row->getData( 'output' ) )
            {
            }
        if ( $unresolved = $row->getData( 'processing' ) )
            {
                $options[ 'option_' . $this->_getProcessingRenderer()->calcOptionHash( $unresolved ) ] = 'selected="selected"';
            }
        $row->setData( 'option_extra_attrs', $options );
    }

}
