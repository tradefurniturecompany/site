<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment;

class Count extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{

    function getDefault()
    {
        return 100000;
    }

    function getName()
    {
        return 'Count';
    }

    function getNote()
    {
        return 'A positive integer specifying the number of records to retain (and delete all others). A negative integer specifying the number of records to delete.';
    }

    function getKey()
    {
        return 'count';
    }

}