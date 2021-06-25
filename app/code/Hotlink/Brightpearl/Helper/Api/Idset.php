<?php
namespace Hotlink\Brightpearl\Helper\Api;

class Idset
{
    const ALL    = '*';
    const SINGLE = 'single';
    const RANGE  = 'range';

    //const MIXED_LIST     = 'mixed_list';
    const ORDERED_LIST   = 'ordered_list';
    const UNORDERED_LIST = 'unordered_list';

    const RANGE_SEPARATOR          = '-';
    const ORDERED_LIST_SEPARATOR   = ',';
    const UNORDERED_LIST_SEPARATOR = '.';

    function single($value)
    {
        return $value;
    }

    function range($left, $right)
    {
        return $left . self::RANGE_SEPARATOR . $right;
    }

    function orderedList(array $values)
    {
        return implode(self::ORDERED_LIST_SEPARATOR, $values);
    }

    function unorderedList(array $values)
    {
        return implode(self::UNORDERED_LIST_SEPARATOR, $values);
    }

}
