<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Model\Form\Element;

class Select extends \Magento\Framework\Data\Form\Element\Select
{
    /**
     * {inheritDoc}
     */
    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html ='<optgroup label="'.$option['label'].'">'."\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .='</optgroup>'."\n";
        } else {
            $html = '<option value="'.$this->_escape($option['value']).'"';
            $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
            $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
            $html.= isset($option['disabled']) ? ' disabled' : '';

            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        }
        return $html;
    }
}
