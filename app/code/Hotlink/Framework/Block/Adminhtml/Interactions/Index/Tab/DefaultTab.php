<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab;

class DefaultTab extends \Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\AbstractTab
{

    protected $_tabHelper = false;

    protected $interactionReflectionHelper;
    protected $factoryHelper;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hotlink\Framework\Helper\Html\Fieldset $interactionHtmlFieldsetHelper,
        \Hotlink\Framework\Model\Config\Map $interactionConfigMap,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Reflection $interactionReflectionHelper,
        array $data = []
    )
    {
        parent::__construct( $context,
                             $interactionHtmlFieldsetHelper,
                             $interactionConfigMap,
                             $data );
        $this->factoryHelper = $factoryHelper;
        $this->interactionReflectionHelper = $interactionReflectionHelper;
    }

    public function initForm( $form, $interaction )
    {
        $id = $this->getTabId();
        $fieldset = $form->addFieldset( $id, array( 'legend' => $interaction->getName(),
                                                    'name'   => $form->getName() ) );
        $this
            ->removeButton( 'create' )
            ->removeButton( 'reset' )
            ->removeButton( 'delete' )
            ->removeButton( 'save' )
            ->removeButton( 'back' );

        $fieldset->addEntity( $interaction );
        $iframe = $this->addFrame( $fieldset );

        $submit = $this->addButtonPostFrame( $fieldset, $iframe, 'Execute', false )->setClass( 'button-half' );
        $collection = $this->addCollectionField( $fieldset, 'collection' );
        $collection->addItem( $submit );
    }

    public function getHeaderText()
    {
        return __( $this->getInteraction()->getName() );
    }

    public function getTabLabel()
    {
        return __(  $this->getInteraction()->getName() );
    }

    public function getTabTitle()
    {
        return __( $this->getInteraction()->getName() );
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabHelper()
    {
        if ( !$this->_tabHelper )
            {
                // TODO: Determine default helper convention for M2
                $module = $this->interactionReflectionHelper->getModule( $this->getInteraction() );
                $model = $this->interactionReflectionHelper->getModelModule( $this->getInteraction() );
                //$this->_tabHelper = Mage::helper( $model );
                // TODO: Variable inside a Mage::helper call not converted: $model
                $this->_tabHelper = $this->factoryHelper->create( $model );
            }
        return $this->_tabHelper;
    }

}
