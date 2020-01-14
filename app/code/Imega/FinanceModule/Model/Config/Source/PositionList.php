<?php

namespace Imega\FinanceModule\Model\Config\Source;

class PositionList implements \Magento\Framework\Option\ArrayInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => 'before', 'label' => __('Before element')],
    ['value' => 'after', 'label' => __('After element')],
    ['value' => 'append', 'label' => __('Append to end of element')],
    ['value' => 'prepend', 'label' => __('Prepend to beginning of element')],
    ['value' => 'replace', 'label' => __('Replace contents of element')]
  ];
 }
}

?>
