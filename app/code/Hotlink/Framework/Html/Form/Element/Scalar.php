<?php
namespace Hotlink\Framework\Html\Form\Element;

class Scalar extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    protected $selectFactory;
    protected $textFactory;

    function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,

        \Magento\Framework\Data\Form\Element\SelectFactory $selectFactory,
        \Magento\Framework\Data\Form\Element\TextFactory $textFactory,
        $data = [])
    {
        $this->selectFactory = $selectFactory;
        $this->textFactory = $textFactory;

        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $data );
    }

    function getElementHtml()
    {
        $value = $this->getValue();
        $parts = explode( ' ', $value );
        $unit = array_pop( $parts );
        $amount = implode( ' ', $parts );

        $text = $this->getTextElement( $amount );

        $units = $this->getValues();

        $select = $this->getSelectElement( $unit, $units );

        $html = $this->getBeforeHtml();
        $html .= $text->getElementHtml();
        $html .= $select->getElementHtml();
        $html .= $this->getAfterHtml();
        return $html;
    }

    function getSelectElement( $value = '', $values = array() )
    {
        $name = $this->getName() . '[units]';
        $id = $this->getId() . '_units';
        $select = $this->selectFactory->create();
        $select
            ->setForm( $this->getForm() )
            ->setId( $id )
            ->setName( $name )
            ->setLabel( null )
            ->setValue( $value )
            ->setValues( $values )
            ->setStyle( 'width:45%' );
        return $select;
    }

    function getTextElement( $value = '' )
    {
        $name = $this->getName() . '[value]';
        $id = $this->getId();
        $text = $this->textFactory->create();
        $text
            ->setForm( $this->getForm() )
            ->setId( $id )
            ->setName( $name )
            ->setLabel( null )
            ->setValue( $value )
            ->setStyle( 'width:45%' );
        return $text;
    }

}