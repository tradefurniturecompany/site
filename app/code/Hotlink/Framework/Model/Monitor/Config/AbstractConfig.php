<?php
namespace Hotlink\Framework\Model\Monitor\Config;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Config\AbstractConfig
{

    protected $interaction;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Config\Field $interactionConfigFieldHelper,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        array $data = []
    )
    {
        $this->interaction = $interaction;
        parent::__construct( $storeManager,
                             $scopeConfig,
                             $interactionConfigFieldHelper,
                             $data );
    }

    protected function _getInteraction()
    {
        return $this->interaction;
    }

}
