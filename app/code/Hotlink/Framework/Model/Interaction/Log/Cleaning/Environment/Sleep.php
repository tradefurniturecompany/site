<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment;

class Sleep extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{

    function getDefault()
    {
        return 5000;
    }

    function getName()
    {
        return 'Sleep between deletes';
    }

    function getNote()
    {
        return 'Millionths of a second (micro seconds)';
    }

    function getKey()
    {
        return 'sleep';
    }

}
