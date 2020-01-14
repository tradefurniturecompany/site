<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Stock\Ttl;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_triggerRenderer;

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn( 'trigger', array(
            'label'    => __( 'Trigger' ),
            'renderer' => $this->_getTriggerRenderer()
        ));

        $this->addColumn( 'ttl', array(
            'label' => __( 'TTL' ),
            'style' => 'width:200px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getTriggerRenderer()
    {
        if ( !$this->_triggerRenderer ) {
            $this->_triggerRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Stock\Trigger', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_triggerRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_triggerRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject
     */
    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $trigger = $row->getData( 'trigger' );
        $options = [];

        if ($trigger) {
            $options['option_'. $this->_getTriggerRenderer()->calcOptionHash( $trigger )] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
