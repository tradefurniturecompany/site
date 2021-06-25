<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class ForceSalesCredit extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Boolean
{
    public function getName()
    {
        return 'Force Sales Credit';
    }

    public function getKey()
    {
        return 'force-salescredit';
    }

    public function getDefault()
    {
        return false;
    }

    public function getNote()
    {
        return 'Recreate a sales credit via the Api even if it has already been exported';
    }

}
