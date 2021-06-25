<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment;

class Skus extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{
    function getDefault()
    {
        return null;
    }

    function getName()
    {
        return 'SKUs';
    }

    function getNote()
    {
        return 'Separate SKUs with a comma (csv). Leave empty to update all products';
    }

    function getKey()
    {
        return 'skus';
    }

    function getValue()
    {
        return $this->_value;
    }
}