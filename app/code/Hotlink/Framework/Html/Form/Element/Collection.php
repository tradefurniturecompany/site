<?php
namespace Hotlink\Framework\Html\Form\Element;

class Collection extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    protected $items = array();

    public function addItem( $item )
    {
        $this->items[] = $item;
        return $this;
    }

    public function getElementHtml()
    {
        $html = '';
        foreach ( $this->items as $item )
            {
                $item->setForm( $this->getForm() );
                $html .= $item->getElementHtml();
            }
        $html.= $this->getAfterElementHtml();
        return $html;
    }

}