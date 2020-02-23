<?php
namespace Hotlink\Framework\Html\Form\Element;

class Button extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    function getElementHtml()
    {
        $block = $this->getBlock( '\Magento\Backend\Block\Widget\Button' );
        $block->setType( $this->getType() )
            ->setData( 'onclick', $this->getData( 'onclick' ) )
            ->setId( $this->getHtmlId() )
            ->setName( $this->getName() )
            ->setClass( $this->getClass() )
            ->setStyle( $this->getStyle() )
            ->setValue( $this->getEscapedValue() )
            ->setDisabled( $this->getDisabled() )
            ->setLabel( $this->getButton() );
        $html = $block->toHtml();
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    private function getBlock( $class )
    {
        if ( $layout = $this->_renderer->getLayout() )
            {
                return $this->_renderer->getLayout()->createBlock( $class );
            }
        else
            {
                throw new \Exception( "Layout fail - invalid block" . ( string ) $class );
            }
    }

}