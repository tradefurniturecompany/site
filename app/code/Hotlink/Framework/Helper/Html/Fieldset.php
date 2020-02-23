<?php
namespace Hotlink\Framework\Helper\Html;

class Fieldset
{

    protected $storeManager;
    protected $interactionFormElementCollectionFactory;
    protected $interactionFormElementButtonFactory;
    protected $interactionFormElementFileFactory;
    protected $interactionFormElementScalarFactory;
    protected $interactionFormElementIframeFactory;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Html\Form\Element\CollectionFactory $interactionFormElementCollectionFactory,
        \Hotlink\Framework\Html\Form\Element\ButtonFactory $interactionFormElementButtonFactory,
        \Hotlink\Framework\Html\Form\Element\FileFactory $interactionFormElementFileFactory,
        \Hotlink\Framework\Html\Form\Element\ScalarFactory $interactionFormElementScalarFactory,
        \Hotlink\Framework\Html\Form\Element\IframeFactory $interactionFormElementIframeFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->interactionFormElementCollectionFactory = $interactionFormElementCollectionFactory;
        $this->interactionFormElementButtonFactory = $interactionFormElementButtonFactory;
        $this->interactionFormElementFileFactory = $interactionFormElementFileFactory;
        $this->interactionFormElementScalarFactory = $interactionFormElementScalarFactory;
        $this->interactionFormElementIframeFactory = $interactionFormElementIframeFactory;
    }

    function getFieldsetElementId( $fieldset, $id, $idsuffix )
    {
        if ( $idsuffix )
            {
                $idsuffix = '.' . $idsuffix;
            }
        return $fieldset->getId() . '.' . $id . $idsuffix;
    }

    function getFieldsetElementName( $fieldset, $name )
    {
        return $name;
        return $fieldset->getName() . '[' . $name . ']';
    }

    function createCollection( $fieldset, $name, $label = '', $value = '', $add = true, $idsuffix = '' )
    {
        $collection = $this->interactionFormElementCollectionFactory->create();
        if ( $label )
            {
                $collection->setLabel( $label );
            }
        return $this->_initElement( $fieldset, $collection, $add, $name, $label, $value );
    }

    function createButton( $fieldset, $name, $label, $value = '', $add = true, $idsuffix = '' )
    {
        $button = $this->interactionFormElementButtonFactory->create();
        return $this->_initElement( $fieldset, $button, $add, $name, $label, $value, $idsuffix = '' );
    }

    function createFile( $fieldset, $name, $label, $value = '', $add = true, $idsuffix = '' )
    {
        $file = $this->interactionFormElementFileFactory->create();
        $file->setLabel( $label );
        return $this->_initElement( $fieldset, $file, $add, $name, $label, $value );
    }

    protected function _initElement( $fieldset, $element, $add, $name, $label, $value, $idsuffix = '' )
    {
        $element->setName( $name )
            ->setButton( $label )
            ->setValue( $value )
            ->setId( $this->getFieldsetElementId( $fieldset, $name, $idsuffix ) )
            ->setName( $this->getFieldsetElementName( $fieldset, $name ) )
            ->setRenderer( \Magento\Framework\Data\Form::getFieldsetElementRenderer() );
        if ( $add ) $fieldset->addElement( $element );
        return $element;
    }

    function createStore( $fieldset, $name, $label, $value = '', $idsuffix = '' )
    {
        $fieldset->addType( 'store', "\Hotlink\Framework\Html\Form\Element\Store" );
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'store',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value
                                            ));
        return $field;
    }

    function createText( $fieldset, $name, $label, $value = '', $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'text',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value
                                            ));
        return $field;
    }

    function createNote( $fieldset, $name, $label, $value = '', $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'note',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'text'  => $value
                                            ));
        return $field;
    }

    function createTextArea( $fieldset, $name, $label, $value = '', $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'textarea',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value
                                            ));
        return $field;
    }

    function createHidden( $fieldset, $name, $value = '', $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'hidden',
                                      array(
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value
                                            ));
        return $field;
    }

    function createSelect( $fieldset, $name, $label, $value = '', $values = array(), $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'select',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value,
                                            'values' => $values
                                            ));
        return $field;
    }

    function createScalar( $fieldset, $name, $label, $value = '', $values = array(), $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $scalar = $this->interactionFormElementScalarFactory->create();
        $scalar
            ->setLabel( $label )
            ->setValues( $values )
            ->setValue( $value )
            ->setId( $this->getFieldsetElementId( $fieldset, $name, $idsuffix ) )
            ->setName( $this->getFieldsetElementName( $fieldset, $name ) )
            ->setRenderer( \Magento\Framework\Data\Form::getFieldsetElementRenderer() );
        $fieldset->addElement( $scalar );
        return $scalar;
    }

    function createMultiSelect( $fieldset, $name, $label, $value = '', $values = array(), $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'multiselect',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value,
                                            'values' => $values
                                            ));
        return $field;
    }

    function createCheckbox( $fieldset, $name, $label, $value = false, $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'checkbox',
                                      array(
                                            'label'  => $label,
                                            'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                            'value'  => $value,
                                            ));
        return $field;
    }

    /**
     * Creates a date(time) field
     * @param ? $fieldset fieldset to attach to
     * @param string $name field name (relative)
     * @param string $label field label
     * @param string $value field initial value
     * @param string $format format (ex. YYYY-MM-DD)
     * @param boolean $required is this field required?
     * @param type $image relative URL of the button icon image (null for default one)
     */
    function createDate( $fieldset, $name, $label, $value='', $format='yyyy-MM-dd', $required=false, $image=null, $idsuffix = '' )
    {
        if ( is_null($image) ) {
            $image = $this->storeManager->getBaseUrl( \Magento\Store\Model\Store::URL_TYPE_SKIN ).'/adminhtml/default/default/images/grid-cal.gif';
        }
        $elementId = $this->getFieldsetElementId( $fieldset, $name, $idsuffix );
        $field = $fieldset->addField( $elementId, 'date', array(
                                                                'label'  => $label,
                                                                'name'   => $this->getFieldsetElementName( $fieldset, $name ),
                                                                'value'  => $value,
                                                                'format' => $format,
                                                                'required' => $required,
                                                                'image' => $image
                                                                ));
        return $field;
    }

    function createFrame( $fieldset, $id, $idsuffix = '' )
    {
        $elementId = $this->getFieldsetElementId( $fieldset, $id, $idsuffix );
        $iframe = $this->interactionFormElementIframeFactory->create();
        $iframe->setId( $elementId );
        $iframe->setName( $elementId );
        $iframe->setForm( $fieldset->getForm() );
        return $iframe;
    }

}
