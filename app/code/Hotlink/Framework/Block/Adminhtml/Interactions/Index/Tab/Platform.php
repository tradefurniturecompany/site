<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab;

class Platform extends \Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\AbstractTab
{

    /**
     * @var \Hotlink\Framework\Helper\Reflection
     */
    protected $interactionReflectionHelper;

    function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hotlink\Framework\Helper\Html\Fieldset $interactionHtmlFieldsetHelper,
        \Hotlink\Framework\Model\Config\Map $interactionConfigMap,
        \Hotlink\Framework\Helper\Reflection $interactionReflectionHelper,
        array $data = []
    )
    {
        $this->interactionReflectionHelper = $interactionReflectionHelper;
        parent::__construct( $context,
                             $interactionHtmlFieldsetHelper,
                             $interactionConfigMap,
                             $data );
    }

    function getTabOrder()
    {
        return 10;
    }

    function initForm( $form, $platform )
    {
        $this->setPlatform( $platform );
        $id = $this->getTabId();

        $fieldset = $form->addFieldset( $id, array( 'legend' => 'Platform', 'name' => $form->getName() ) );

        $this
            ->removeButton( 'create' )
            ->removeButton( 'reset' )
            ->removeButton( 'delete' )
            ->removeButton( 'save' )
            ->removeButton( 'back' );

        $this->addNoteField( $fieldset, 'platform', "Platform", $this->getPlatform()->getName() );
        $this->addNoteField( $fieldset, 'module', "Module", $this->getModule() );
        $this->addNoteField( $fieldset, 'version', "Version", $this->getPlatform()->getVersion() );
    }

    function getHeaderText()
    {
        return __( $this->getPlatform()->getName() );
    }

    function getTabLabel()
    {
        return __( "Installation" );
    }

    function getTabTitle()
    {
        return __( "Platform" );
    }

    protected function getModule()
    {
        return $this->interactionReflectionHelper->getModule( $this->getPlatform() );
    }

}
