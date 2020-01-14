<?php
namespace Hotlink\Framework\Setup;

class AbstractData
{

    protected $platform;
    protected $resourceConfig;

    public function __construct( \Hotlink\Framework\Model\Platform $platform,
                                 \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
    )
    {
        $this->platform = $platform;
        $this->resourceConfig = $resourceConfig;
    }

    protected function _init()
    {
        if ( ! $this->platform->getId() )
            {
                $this->resourceConfig->saveConfig(
                    implode( '/', [ $this->platform->getSection(), $this->platform->getGroup(), $this->platform->getField() ] ),
                    uniqid( true ),
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID );
            }
    }

}
