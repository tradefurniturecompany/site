<?php
namespace Hotlink\Framework\Model\Config\Field\Identifier\Customer;

class Backend extends \Magento\Framework\App\Config\Value
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Hotlink\Framework\Helper\Exception
     */
    protected $interactionExceptionHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Exception $interactionExceptionHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->interactionExceptionHelper = $interactionExceptionHelper;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }


    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ( ( $value == \Hotlink\Framework\Model\Config\Field\Identifier\Source::ID_INCREMENT )
             && ( !$this->scopeConfig->getValue( 'customer/create_account/generate_human_friendly_id' , \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ) )
            {
                $this->interactionExceptionHelper->throwConfiguration( '<br/><b>[Customer Identifier]</b> To use Increment ID, the generation of increment ID\'s must be enabled via: <br/>System/Configuration/Customers/Customer Configuration/Create New Account Options/Generate Human-Friendly Customer ID.', $this );
            }
        return parent::_beforeSave();
    }
}
