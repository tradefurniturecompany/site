<?php
namespace Hotlink\Framework\Block\Adminhtml\Interactions\Index;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    const HTML_SEPARATOR_HOTLINK = '__';
    const HTML_SEPARATOR_PLATFORM = '_';
    const HTML_SEPARATOR_INTERACTION = '_';
    
    protected $registry;
    protected $storeManager;
    protected $factoryHelper;
    protected $interactionFormFactory;
    protected $htmlHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,
        \Hotlink\Framework\Html\FormFactory $interactionFormFactory,
        array $data = []
    )
    {
        $this->interactionFormFactory = $interactionFormFactory;
        $this->registry = $registry;
        $this->factoryHelper = $factoryHelper;
        $this->htmlHelper = $htmlHelper;
        parent::__construct(
            $context,
            $jsonEncoder,
            $authSession,
            $data
        );

        $this->storeManager = $context->getStoreManager();

        $this->setId( 'interaction_tabs' );
        $this->setDestElementId( 'interaction_content' );   // Matched element id in "Index.php"
        $this->setTitle( $this->_getTitle() );
    }

    protected function _getHtmlHelper()
    {
        return $this->htmlHelper;
    }

    protected function _getPlatform()
    {
        return $this->registry->registry( 'current_platform' );
    }

    protected function _getPlatformHtmlId( $suffix = '' )
    {
        $code = $this->_getPlatform()->getCode();
        $id = 'hotlink' . self::HTML_SEPARATOR_HOTLINK
            . 'platform' . self::HTML_SEPARATOR_PLATFORM . $code;
        if ( $suffix )
            {
                $id .= self::HTML_SEPARATOR_PLATFORM . $suffix;
            }
        return $id;
    }

    protected function _getInteractionHtmlId( $interaction, $suffix = '' )
    {
        $code = $this->_getHtmlHelper()->encode( $interaction );
        $id = 'hotlink' . self::HTML_SEPARATOR_HOTLINK . $code;
        if ( $suffix )
            {
                $id .= self::HTML_SEPARATOR_INTERACTION . $suffix;
            }
        return $id;
    }

    protected function _prepareLayout()
    {
        $tabs = array_merge( [ $this->_getInstallationTab() ], $this->_getInteractionTabs() );
        foreach( $tabs as $tab )
            {
                $this->addTab( $tab->getTabId(), $tab );
            }
        return parent::_prepareLayout();
    }

    protected function _getInteractionTabs()
    {
        $tabs = [];
        $interactionClasses = $this->_getPlatform()->getInteractions();
        foreach ( $interactionClasses as $interactionClass )
            {
                $interaction = $this->factoryHelper->create( $interactionClass );
                $interaction->createEnvironment( $this->storeManager->getStore()->getStoreId() );

                $interactionTabId = $this->_getInteractionHtmlId( $interaction );
                $interactionTab = $this->getLayout()->createBlock( $interaction->getTabBlock() );
                $interactionTab->setTabId( $interactionTabId );
                $interactionTab->setNameInLayout( $interactionTabId );

                $interactionFormId = $this->_getInteractionHtmlId( $interaction, 'form' );
                $interactionForm = $this->interactionFormFactory->create();
                $interactionForm->setUseContainer( true )
                    ->setEnctype( 'multipart/form-data' )
                    ->setMethod( 'post' )
                    ->setOnsubmit( '' )
                    ->setId( $interactionFormId )    // TODO: check, was $interactionTabId
                    ->setName( \Hotlink\Framework\Model\Trigger\Admin\User\Request::FORM_NAME )
                    ->setAction( $this->getActionUrl() );

                $interactionTabFormId = $this->_getInteractionHtmlId( $interaction, 'tab-form' );
                $interactionTabForm = $this->getLayout()->createBlock( '\Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\Form' );
                $interactionTabForm->setNameInLayout( $interactionTabFormId );
                $interactionTabForm->setForm( $interactionForm );

                $interactionTab->setChild( 'form', $interactionTabForm );
                $interactionTab->setInteraction( $interaction );
                $interactionTab->initForm( $interactionForm, $interaction );

                $tabs[] = $interactionTab;
            }
        return $tabs;
    }

    protected function _getInstallationTab()
    {
        $idInstallationTab = $this->_getPlatformHtmlId( 'tab' );
        $idInstallationForm = $this->_getPlatformHtmlId( 'form' );
        $idInstallationTabForm = $this->_getPlatformHtmlId( 'tab-form' );

        $tab = $this->getLayout()->createBlock( '\Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\Platform', $idInstallationTab );
        $tab->setTabId( $idInstallationTab );

        $formElement = $this->_createFormElement( null, $idInstallationForm, \Hotlink\Framework\Model\Trigger\Admin\User\Request::FORM_NAME );
        $formElement->setNameInLayout( $idInstallationForm );

        $tabForm = $this->getLayout()->createBlock( '\Hotlink\Framework\Block\Adminhtml\Interactions\Index\Tab\Form', $idInstallationTabForm );
        $tabForm->setNameInLayout( $idInstallationTabForm );
        $tabForm->setForm( $formElement );

        $tab->setChild( 'form', $tabForm );
        $tab->initForm( $formElement, $this->_getPlatform() );
        $tab->setActiveTab( $tab->getTabId() );
        return $tab;
    }

    protected function _createFormElement( $interaction, $id, $name )
    {
        $form = $this->interactionFormFactory->create();
        $form->setUseContainer( true )
            ->setEnctype( 'multipart/form-data' )
            ->setMethod( 'post' )
            ->setOnsubmit( '' )
            ->setId( $id )
            ->setName( $name )
            ->setAction( $this->getActionUrl() );
        return $form;
    }

    protected function getActionUrl()
    {
        return $this->getUrl( '*/*/execute' );
    }

    protected function _getTitle()
    {
        return $this->_getPlatform()->getName() . ' ' . __( 'Interactions');
    }

}
