<?php
namespace Hotlink\Framework\Helper\Html\Form\Filter;

class Magento extends \Hotlink\Framework\Helper\Html\Form\Filter
{

    protected $reflectionHelper;
    protected $magentoFilterFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $htmlFieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,

        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Model\Filter\MagentoFactory $magentoFilterFactory
    )
    {
        $this->reflectionHelper = $reflectionHelper;
        $this->magentoFilterFactory = $magentoFilterFactory;

        parent::__construct(
            $exceptionHelper,
            $htmlFieldsetHelper,
            $htmlHelper );
    }

    function getHtmlKey()
    {
        return 'filter_magento';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Filter\Magento $filter, $params = array() )
    {
        $helper = $this->reflectionHelper->getHelperName( $this );
        $name = $fieldset->getName();
        $field = $filter->getField();
        $options = $this->arrayToOptionArray( $filter->getFields() );

        $selectorVisible = isset( $params[ 'selector_visible' ] ) ? $params[ 'selector_visible' ] : true;
        $selectorLabel = isset( $params[ 'selector_label' ] ) ? $params[ 'selector_label' ] : 'Field to use';
        $idLabel = isset( $params[ 'id_label' ] ) ? $params[ 'id_label' ] : 'Comma Separated Id(s)';

        if ( !$selectorVisible )
            {
                $this->getFieldHelper()
                    ->createHidden( $fieldset, 'filter_field', $field )
                    ->setName( "$name"."[filter][$helper][field]" );
            }
        else
            {
                $this->getFieldHelper()
                    ->createSelect( $fieldset, 'filter_field', $selectorLabel, $field, $options )
                    ->setName( "$name"."[filter][$helper][field]" );
            }
        $this->getFieldHelper()
            ->createText( $fieldset, 'filter_id', $idLabel, $this->arrayToCsv( $filter->getIdentifiers() ) )
            ->setName( "$name"."[filter][$helper][id]" );
        $this->getFieldHelper()
            ->createHidden( $fieldset, 'filter_model', $filter->getModel() )
            ->setName( "$name"."[filter][$helper][model]" );
    }

    function getObject( $data, $environment )
    {
        $filter = $this->magentoFilterFactory->create();
        $filter->setModel( $data[ 'model' ] )
            ->setField( $data[ 'field' ] )
            ->setIdentifiers( $this->csvToArray( $data[ 'id' ] ) );
        return $filter;
    }

}