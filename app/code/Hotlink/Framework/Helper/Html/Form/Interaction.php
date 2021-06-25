<?php
namespace Hotlink\Framework\Helper\Html\Form;

class Interaction extends \Hotlink\Framework\Helper\Html\Form\AbstractForm
{

    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $interactionHtmlFieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper
    )
    {
        $this->factoryHelper = $factoryHelper;
        parent::__construct( $interactionExceptionHelper,
                             $interactionHtmlFieldsetHelper,
                             $htmlHelper );
    }

    public function getHtmlKey()
    {
        return 'interaction';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $fieldset->setName( $fieldset->getForm()->getName() );

        $this->_initObjectHeader( $interaction, $fieldset );

        $restoreName = $fieldset->getName();
        $fieldset->setName( $this->_getHtmlNameData( $fieldset ) );
        $fieldset->addEntity( $interaction->getEnvironment() );
        $fieldset->setName( $restoreName );
    }

    public function getObject( $form, $parent )
    {
        $interaction = $this->factoryHelper->create( $this->_getClass( $form ) );
        if ( $data = $this->_getData( $form ) )
            {
                foreach ( $data as $helper => $values )
                    {
                        $helper = $this->_decode( $helper );
                        $object = $this->factoryHelper->create( $helper )->getObject( $values, $interaction );
                        if ( $object instanceof \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment )
                            {
                                $interaction->setEnvironment( $object );
                            }
                    }
            }
        return $interaction;
    }

}
