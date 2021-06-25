<?php
namespace Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import;

class Config extends \Hotlink\Brightpearl\Model\Interaction\Shipment\Config\AbstractConfig
{
    public function getSortBy($storeId = null)
    {
        return $this->getConfigData('sort_by', $storeId);
    }

    public function getSortDirection($storeId = null)
    {
        return $this->getConfigData('sort_direction', $storeId);
    }
}
