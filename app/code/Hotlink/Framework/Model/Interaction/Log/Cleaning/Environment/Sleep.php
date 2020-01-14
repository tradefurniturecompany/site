<?php
namespace Hotlink\Framework\Model\Interaction\Log\Cleaning\Environment;

class Sleep extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{

    public function getDefault()
    {
        return 5000;
    }

    public function getName()
    {
        return 'Sleep between deletes';
    }

    public function getNote()
    {
        return 'Millionths of a second (micro seconds)';
    }

    public function getKey()
    {
        return 'sleep';
    }

}
