<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Price\Group;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_websiteRenderer;
    protected $_customerGroupRenderer;
    protected $_listRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::WEBSITE,
                          [ 'label' => __( 'Website [Currency]' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getWebsiteRenderer() ]);

        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::CUSTOMER_GROUP,
                          [ 'label' => __( 'Customer group' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getCustomerGroupRenderer() ] );

        $this->addColumn( \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST,
                          [ 'label' => __( 'Apply Brightpearl price list' ),
                            'style' => 'width:200px',
                            'renderer' => $this->_getListRenderer() ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getWebsiteRenderer()
    {
        if ( !$this->_websiteRenderer ) {
            $this->_websiteRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Website', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_websiteRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_websiteRenderer;
    }

    protected function _getCustomerGroupRenderer()
    {
        if ( !$this->_customerGroupRenderer ) {
            $this->_customerGroupRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Customer\Group', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_customerGroupRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_customerGroupRenderer;
    }

    protected function _getListRenderer()
    {
        if ( !$this->_listRenderer ) {
            $this->_listRenderer = $this->getLayout()->createBlock('\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Price\ListPrice', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_listRenderer->setExtraParams( 'style="width:200px"' );
        }
        return $this->_listRenderer;
    }

    protected function _prepareArrayRow( \Magento\Framework\DataObject $row )
    {
        $options = [];

        $website   = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::WEBSITE);
        $cGroup    = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::CUSTOMER_GROUP);
        $priceList = $row->getData(\Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST);

        $options[ 'option_' . $this->_getWebsiteRenderer()->calcOptionHash( $website) ] = 'selected="selected"';
        $options[ 'option_' . $this->_getCustomerGroupRenderer()->calcOptionHash( $cGroup) ] = 'selected="selected"';
        $options[ 'option_' . $this->_getListRenderer()->calcOptionHash( $priceList) ] = 'selected="selected"';

       $row->setData('option_extra_attrs', $options);
    }
}