<?php
namespace Hotlink\Framework\Block\Adminhtml\Tab;

abstract class Base extends \Magento\Backend\Block\Widget\Form\Container implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_frames = [ 'before' => [], 'after' => [] ];

    protected $fieldsetHtmlHelper;

    function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHtmlHelper,
        array $data = []
    ) {
        $this->fieldsetHtmlHelper = $fieldsetHtmlHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _toHtml()
    {
        $html = '';
        $html .= $this->getFramesHtml( 'before' );
        $html .= parent::_toHtml();
        $html .= $this->getFramesHtml( 'after' );
        return $html;
    }

    protected function getFramesHtml( $location )
    {
        $html = '';
        foreach ( $this->_frames[ $location ] as $frame )
            {
                $html .= $frame->getElementHtml();
            }
        return $html;
    }

    protected function getFieldsetHelper()
    {
        return $this->fieldsetHtmlHelper;
    }

    function addCollectionField( $fieldset, $name, $label = '', $value = '', $add = true )
    {
        return $this->getFieldsetHelper()->createCollection( $fieldset, $name, __( $label ), $value, $add );
    }

    function addButtonField( $fieldset, $name, $label, $value = '', $add = true )
    {
        return $this->getFieldsetHelper()->createButton( $fieldset, $name, __( $label ), $value, $add );
    }

    function addFileField( $fieldset, $name, $label, $value = '', $add = true )
    {
        return $this->getFieldsetHelper()->createFile( $fieldset, $name, __( $label ), $value, $add );
    }

    function addTextField( $fieldset, $name, $label, $value = '' )
    {
        return $this->getFieldsetHelper()->createText( $fieldset, $name, __( $label ), $value );
    }

    function addNoteField( $fieldset, $name, $label, $value = '' )
    {
        return $this->getFieldsetHelper()->createNote( $fieldset, $name, __( $label ), $value );
    }

    function addHiddenField( $fieldset, $name, $value = '' )
    {
        return $this->getFieldsetHelper()->createHidden( $fieldset, $name, $value );
    }

    function addSelectField( $fieldset, $name, $label, $value = '', $values = array() )
    {
        return $this->getFieldsetHelper()->createSelect( $fieldset, $name, __( $label ), $value, $values );
    }

    protected function addFrameField( $fieldset, $id, $before = false )
    {
        $iframe = $this->getFieldsetHelper()->createFrame( $fieldset, $id );
        $position = ( $before ) ? 'before' : 'after';
        $this->_frames[ $position ][] = $iframe;
        return $iframe;
    }

    function addDateField( $fieldset, $name, $label, $value='', $format='yyyy-MM-dd', $required=false, $image=null )
    {
        return $this->getFieldsetHelper()->createDate( $fieldset, $name, __( $label ), $value, $format, $required, $image );
    }

}
