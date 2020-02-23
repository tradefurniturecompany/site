<?php
namespace Hotlink\Framework\Block\Adminhtml\System\Config\Form\Field;

class Scalar extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $scalarElementFactory;

    function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hotlink\Framework\Html\Form\Element\ScalarFactory $scalarElementFactory,
        array $data = []
    ) {
        $this->scalarElementFactory = $scalarElementFactory;
        parent::__construct(
            $context,
            $data
        );
    }


    function render( \Magento\Framework\Data\Form\Element\AbstractElement $element )
    {
        $scalar = $this->scalarElementFactory->create();
        $scalar->setForm( $element->getForm() );
        $scalar->setData( $element->getData() );
        $scalar->setId( $element->getId() );
        return parent::render( $scalar );
    }

}