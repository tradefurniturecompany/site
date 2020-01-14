<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Price\Attibute;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_attributeRenderer;
    protected $_listRenderer;
    protected $_behaviourRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::ATTRIBUTE_CODE,
                          [ 'label' => __( 'Magento Attribute' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getAttributeRenderer() ]);

        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST,
                          [ 'label' => __( 'Apply Brightpearl price list' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getListRenderer() ]);

        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::BEHAVIOUR,
                          [ 'label' => __( 'Missing value behaviour' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getBehaviourRenderer() ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getAttributeRenderer()
    {
        if ( !$this->_attributeRenderer ) {
            $this->_attributeRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Attribute', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_attributeRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_attributeRenderer;
    }

    protected function _getListRenderer()
    {
        if ( !$this->_listRenderer ) {
            $this->_listRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price\ListPrice', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_listRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_listRenderer;
    }

    protected function _getBehaviourRenderer()
    {
        if ( !$this->_behaviourRenderer ) {
            $this->_behaviourRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price\Behaviour', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_behaviourRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_behaviourRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];

        $attributeCode = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::ATTRIBUTE_CODE);
        $priceList     = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST);
        $behaviour     = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::BEHAVIOUR);

        $options[ 'option_' . $this->_getAttributeRenderer()->calcOptionHash( $attributeCode) ] = 'selected="selected"';
        $options[ 'option_' . $this->_getListRenderer()->calcOptionHash( $priceList) ] = 'selected="selected"';
        $options[ 'option_' . $this->_getBehaviourRenderer()->calcOptionHash( $behaviour) ] = 'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }
}