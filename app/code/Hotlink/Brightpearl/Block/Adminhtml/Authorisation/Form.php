<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\Authorisation;

class Form extends \Magento\Backend\Block\Widget\Form
{
    protected $formFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $form->setMethod( 'post' )
            ->setId( 'edit_form' )
            ->setAction( $this->getUrl( '*/*/locate', array() ) )
            ->setUseContainer( true );

        $fieldset = $form->addFieldset(
            'auth',
            [ 'legend' => '',
              'class' => 'fieldset-wide' ] );

        $fieldset->addField(
            'accountCode',
            'text',
            [ 'name' => 'accountCode',
              'label' => __( 'Account code' ),
              'required' => true,
              'class' => 'validate-not-empty' ] );

        $fieldset->addField(
            'accountCodeNote',
            'note',
            [ 'name' => 'accountCodeNote',
              'label' => '',
              'text' => __( 'Please enter your Account code as supplied by Brightpearl and press '.
                            'Submit to proceed with the authorisation process.' ) ] );

        $this->setForm( $form );

        return parent::_prepareForm();
    }
}