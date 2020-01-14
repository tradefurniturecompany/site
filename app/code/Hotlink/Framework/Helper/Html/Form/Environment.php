<?php
namespace Hotlink\Framework\Helper\Html\Form;

class Environment extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
{

    protected $storeManager;
    protected $factoryFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $interactionHtmlFieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,
        \Hotlink\Framework\Helper\Factory $factoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->factoryFactory = $factoryFactory;
        $this->storeManager = $storeManager;
        parent::__construct( $interactionExceptionHelper,
                             $interactionHtmlFieldsetHelper,
                             $htmlHelper );
    }

    public function getHtmlKey()
    {
        return 'environment';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    {
        $this->_initObjectHeader( $environment, $fieldset );
        $parameters = $environment->getParameters();
        if ( $parameters && count( $parameters ) > 0 )
            {
                $restoreName = $fieldset->getName();
                $fieldset->setName( $this->_getHtmlNameData( $fieldset ) );
                foreach ( $parameters as $parameter )
                    {
                        $parameter->getFormHelper()->addFields( $fieldset, $parameter );
                    }
                $fieldset->setName( $restoreName );
            }
    }

    public function getObject( $form, $interaction )
    {
        $storeId = false;
        $parameters = [];

        if ( $data = $this->_getData( $form ) )
            {
                foreach ( $data as $helper => $values )
                    {
                        $parts = explode( '|', $helper );
                        $helper = $this->_decode( $parts[ 0 ] );
                        $parameter = $this->factoryFactory->create( $helper )->getObject( $values, null );
                        if ( $parameter instanceof \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter )
                            {
                                if ( $parameter->getKey() == 'store' )
                                    {
                                        $storeId = $parameter->getValue();
                                    }
                                else
                                    {
                                        $parameters[] = $parameter;
                                    }
                            }
                    }
            }

        $storeId = ( $storeId === false ) ? $this->storeManager->getStore()->getStoreId() : $storeId;
        $environment = $this->factoryFactory->create( $this->_getClass( $form ), [ 'interaction' => $interaction, 'storeId' => $storeId ] );
        foreach ( $parameters as $parameter )
            {
                $environment->addParameter( $parameter );
            }

        return $environment;
    }

}