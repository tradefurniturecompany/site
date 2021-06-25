<?php
namespace Hotlink\Framework\Model\Interaction\Action\Config;

class AbstractConfig extends \Hotlink\Framework\Model\Interaction\Config\AbstractConfig
{

    public function getScopeType()
    {
        return \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
    }

    public function getScope( $storeId )
    {
        return null;
    }

}
