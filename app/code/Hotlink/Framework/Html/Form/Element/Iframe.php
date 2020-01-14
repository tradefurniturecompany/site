<?php
namespace Hotlink\Framework\Html\Form\Element;

class Iframe extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        $data = []
    )
    {
        parent::__construct( $factoryElement,
                             $factoryCollection,
                             $escaper,
                             $data );
        $this->setSrc( "" );
        $this->setFrameborder( 0 );
    }

    public function getHtmlAttributes()
    {
        return array( 'class', 'style', 'width', 'onclick', 'onchange', 'frameborder', 'src' );
    }

    protected function getHtmlDivAttributes()
    {
        return array( 'class', 'style' );
    }

    public function getElementHtml()
    {
        $html = '<iframe id="'.$this->getHtmlId() . '" name="'.$this->getName().'" '
            . $this->serialize( $this->getHtmlAttributes() )
            . '></iframe>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

}