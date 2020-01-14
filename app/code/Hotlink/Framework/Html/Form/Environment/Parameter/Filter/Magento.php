<?php
namespace Hotlink\Framework\Html\Form\Environment\Parameter\Filter;

class Magento extends \Hotlink\Framework\Html\Form\Environment\Parameter\Filter\AbstractFilter
{

    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,

        \Hotlink\Framework\Helper\Factory $factoryHelper
    )
    {
        $this->factoryHelper = $factoryHelper;
        parent::__construct( $exceptionHelper, $fieldsetHelper, $htmlHelper );
    }

    public function getHtmlKey()
    {
        return 'parameter_filter_magento';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter\Magento $parameter )
    {
        $this->_initObjectHeader( $parameter, $fieldset );

        $htmlName = $this->_getHtmlNameData( $fieldset );

        $field = $parameter->getValue()->getField();
        $fieldOptions = $this->arrayToOptionArray( $parameter->getValue()->getFields() );
        $attributeOptions = $this->arrayToOptionArray( array_flip( $parameter->getValue()->getAttributes() ) );

        if ( $parameter->getSelectorVisible() )
            {
                $selectorLabel = ( $parameter->hasSelectorLabel() ) ? $parameter->getSelectorLabel() : 'Field to use';
                $this->getFieldHelper()
                    ->createSelect( $fieldset, $htmlName . '[filter_field]', $selectorLabel, $field, $fieldOptions )
                    ->setNote( $parameter->getSelectorNote() );
            }
        else
            {
                $this->getFieldHelper()
                    ->createHidden( $fieldset, $htmlName . '[filter_field]', $field );
            }

        if ( $parameter->getAttributesVisible() )
            {
                $attributesLabel = ( $parameter->hasAttributesLabel() ) ? $parameter->getAttributesLabel() : 'Attributes to include';
                $this->getFieldHelper()
                    ->createMultiSelect( $fieldset, $htmlName . '[attribute_field]', $attributesLabel, $field, $attributeOptions )
                    ->setNote( $parameter->getAttributesNote() );
            }
        else
            {
                $this->getFieldHelper()
                    ->createHidden( $fieldset, $htmlName . '[attribute_field]', $field );
            }

        $idLabel = ( $parameter->hasIdsLabel() )
            ? $parameter->getIdsLabel()
            : $parameter->getName();

        $this->getFieldHelper()
            ->createText( $fieldset, $htmlName . '[filter_ids]', $idLabel, $this->arrayToCsv( $parameter->getValue()->getIdentifiers() ) )
            ->setNote( $parameter->getIdsNote() );
        $this->getFieldHelper()
            ->createHidden( $fieldset, $htmlName . '[filter_model]', $parameter->getValue()->getModel() );
    }

    public function getObject( $form, $environment )
    {
        $parameter = $this->factoryHelper->create( $this->_getClass( $form ) );
        if ( $data = $this->_getData( $form ) )
            {
                $attributeField = ( is_array( $data[ 'attribute_field' ] ) )
                    ? $data[ 'attribute_field' ]
                    : array( '*' );
                $filter = $parameter->getValue();
                $filter->setModel( $data[ 'filter_model' ] )
                    ->setField( $data[ 'filter_field' ] )
                    ->setAttributes( $attributeField )
                    ->setIdentifiers( $this->csvToArray( $data[ 'filter_ids' ] ) );
            }
        return $parameter;
    }

}