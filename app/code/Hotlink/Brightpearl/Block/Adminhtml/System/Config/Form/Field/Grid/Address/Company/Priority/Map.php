<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Address\Company\Priority;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_companyRenderer;

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn( 'type', array(
            'label'    => __( 'Magento source' ),
            'renderer' => $this->_getMageStatusRenderer()
        ));

        $this->addColumn( 'code', array(
            'label' => __( 'Attribute code' )
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getMageStatusRenderer()
    {
        if ( !$this->_companyRenderer ) {
            $this->_companyRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Address\Company', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_companyRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_companyRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];

        if ( $type = $row->getData( 'type' ) ) {
            $options[ 'option_' . $this->_getMageStatusRenderer()->calcOptionHash( $type) ] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
