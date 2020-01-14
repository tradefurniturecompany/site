<?php
namespace Hotlink\Framework\Model\Platform;

class Data extends \Hotlink\Framework\Model\Api\Data
{

    public function getMappings()
    {
        return \Hotlink\Framework\Model\Platform\Type::getMappings();
    }

}