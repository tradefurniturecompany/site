<?php
namespace Hotlink\Framework\Html\Form\Environment\Parameter;

class Scopes extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
{
    protected $_parameter;
    protected $universalFactory;
    protected $storeElement;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,

        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Hotlink\Framework\Html\Form\Element\Store $storeElement
        )
    {
        $this->universalFactory = $universalFactory;
        $this->storeElement = $storeElement;

        parent::__construct(
            $interactionExceptionHelper,
            $fieldsetHelper,
            $htmlHelper );
    }

    public function getHtmlKey()
    {
        return 'scopes';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scopes $parameter )
    {
        $this->_parameter = $parameter;
        $this->_initObjectHeader( $parameter, $fieldset );

        $label = ( $parameter->hasLabel() ) ? $parameter->getLabel() : $parameter->getName();
        $value = $parameter->getValue();

        $name = $this->_getHtmlNameData( $fieldset ) . '[value]';

        $field = $this->getFieldHelper()->createStore( $fieldset, $name, $label, $value );
        $size = $parameter->getSize();
        if ( !$size )
            {
                $size = count( $values ) + 1;
                $size = min( $size, 10 );
            }
        $field->setSize( $size );
        $field->setAdminEnabled( $parameter->getAdminEnabled() );
        $field->setAdminVisible( $parameter->getAdminVisible() );
        $field->setWebsitesEnabled( $parameter->getWebsitesEnabled() );
        $field->setWebsitesVisible( $parameter->getWebsitesVisible() );
        $field->setGroupsEnabled( $parameter->getGroupsEnabled() );
        $field->setGroupsVisible( $parameter->getGroupsVisible() );
        $field->setStoresEnabled( $parameter->getStoresEnabled() );
        $field->setStoresVisible( $parameter->getStoresVisible() );
        $field->setMultiselect( $parameter->getMultiselect() );
    }

    public function getObject( $form, $environment )
    {
        $store = $this->universalFactory->create( $this->_getClass( $form ) );
        if ( $data = $this->_getData( $form ) )
            {
                $ids = array();
                if ( isset( $data[ 'value' ] ) )
                    {
                        $values = $data[ 'value' ];
                        if ( !is_array( $values ) )  // Multiselect was not used
                            {
                                $values = array( $values );
                            }
                        foreach ( $values as $code )
                            {
                                $scope = $this->storeElement->getScope( $code );
                                $id = $this->storeElement->getScopeId( $code );
                                $name = $this->storeElement->getScopeCode( $code );
                                if  ( ( $scope !== false ) && ( $id !== false ) )
                                    {
                                        if ( !array_key_exists( $scope, $ids ) )
                                            {
                                                $ids[ $scope ] = array();
                                            }
                                        if ( !in_array( $name, $ids[ $scope ] ) )
                                            {
                                                $ids[ $scope ][ $name ] = $id;
                                            }
                                    }
                            }
                    }
                $store->setValue( $ids );
            }
        else
            {
                if ($store->getMultiselect())
                    {
                        $store->setValue( array() );
                    }
                else
                    {
                        $store->setValue( null );
                    }
            }
        return $store;
    }

}