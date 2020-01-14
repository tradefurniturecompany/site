<?php

namespace Imega\FinanceModule\Model\Config\Source;

class FinanceProviders implements \Magento\Framework\Option\ArrayInterface
{
  public function toOptionArray()
  {
    return array(
      array('value'=>'imegamedia', 'label'=>'imegamedia (TEST)'),
      array('value'=>'afforditnow', 'label'=>'Afford It Now'),
      array('value'=>'cbrf', 'label'=>'Close Brothers'),
      array('value'=>'deko', 'label'=>'Deko Pay'),
      array('value'=>'divido', 'label'=>'Divido'),
      array('value'=>'duologi', 'label'=>'Duologi'),
      array('value'=>'hitachi', 'label'=>'Hitachi PayByFinance'),
      array('value'=>'klarna', 'label'=>'Klarna'),
      array('value'=>'omnicapital', 'label'=>'OmniCapital'),
      array('value'=>'snap', 'label'=>'Snap Finance'),
      array('value'=>'snapuk', 'label'=>'Snap Finance UK'),
      array('value'=>'v12', 'label'=>'V12'),


    );
  }
}

?>
