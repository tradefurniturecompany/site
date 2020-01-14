<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment;

class Count extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{

    public function getDefault()
    {
        return 100000;
    }

    public function getName()
    {
        return 'Count';
    }

    public function getNote()
    {
        return 'A positive integer specifying the number of records to retain (and delete all others). A negative integer specifying the number of records to delete.';
    }

    public function getKey()
    {
        return 'count';
    }

}