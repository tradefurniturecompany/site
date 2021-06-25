<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment;

class Skus extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    public function getDefault()
    {
        return null;
    }

    public function getName()
    {
        return 'SKUs';
    }

    public function getNote()
    {
        return 'Separate SKUs with a comma (csv). Leave empty to update all products';
    }

    public function getKey()
    {
        return 'skus';
    }

    public function getValue()
    {
        return $this->_value;
    }
}