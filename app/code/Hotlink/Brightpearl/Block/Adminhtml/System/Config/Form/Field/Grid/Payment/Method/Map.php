<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Grid\Payment\Method;

class Map extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_magePaymentRenderer;
    protected $_bpPaymentRenderer;
    protected $_bpReceiptRenderer;

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn( 'magento', array(
            'label'    => __( 'Magento payment' ),
            'renderer' => $this->_getMagePaymentRenderer()
        ));

        $this->addColumn( 'brightpearl', array(
            'label' => __( 'Brightpearl nominal code' ),
            'renderer' => $this->_getBpPaymentRenderer()
        ));

        $this->addColumn( 'receipt', array(
            'label' => __( 'Create sales receipts' ),
            'renderer' => $this->_getBpReceiptRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    protected function _getMagePaymentRenderer()
    {
        if ( !$this->_magePaymentRenderer ) {
            $this->_magePaymentRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Payment\Method', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_magePaymentRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_magePaymentRenderer;
    }

    protected function _getBpPaymentRenderer()
    {
        if ( !$this->_bpPaymentRenderer ) {
            $this->_bpPaymentRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Brightpearl\Payment\Method', '', [ 'data' => [ 'is_render_to_js_template' => true ] ]);
            $this->_bpPaymentRenderer->setExtraParams( 'style="width:250px"' );
        }
        return $this->_bpPaymentRenderer;
    }

    protected function _getBpReceiptRenderer()
    {
        if ( !$this->_bpReceiptRenderer ) {
            $this->_bpReceiptRenderer = $this->getLayout()->createBlock( '\Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Yesno', '', [ 'data' => [ 'is_render_to_js_template' => true ] ] );
            $this->_bpReceiptRenderer->setExtraParams( 'style="width:150px"' );
        }
        return $this->_bpReceiptRenderer;
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
            $options[ 'option_' . $this->_getMagePaymentRenderer()->calcOptionHash( $magento )] = 'selected="selected"';
        }

        if ( $brightpearl = $row->getData( 'brightpearl' ) ) {
            $options[ 'option_' . $this->_getBpPaymentRenderer()->calcOptionHash( $brightpearl ) ] = 'selected="selected"';
        }

        if ( $receipt = $row->getData( 'receipt' ) ) {
            $options[ 'option_' . $this->_getBpReceiptRenderer()->calcOptionHash( $receipt ) ] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
