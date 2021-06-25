<?php
namespace Hotlink\Brightpearl\Model\Config;

abstract class AbstractConfig extends \Hotlink\Framework\Model\Config\AbstractConfig
{

    protected $magentoConfig;
    protected $magentoConfigFactory;
    protected $brightpearlConfigBackendHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Hotlink\Framework\Helper\Config\Field $configFieldHelper,

        \Magento\Config\Model\Config\Factory $magentoConfigFactory,
        \Hotlink\Brightpearl\Helper\Config\Backend $brightpearlConfigBackendHelper,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = []
    )
    {
        $this->magentoConfigFactory = $magentoConfigFactory;
        $this->encryptor = $encryptor;
        $this->brightpearlConfigBackendHelper = $brightpearlConfigBackendHelper;

        parent::__construct($storeManager, $scopeConfig, $configFieldHelper, $data);
    }

    protected function _getSection()
    {
        return 'hotlink_brightpearl';
    }

    protected function getMagentoConfig()
    {
        if ( !$this->magentoConfig )
            {
                $this->magentoConfig = $this->magentoConfigFactory->create();
            }
        return $this->magentoConfig;
    }

    protected function saveValue($value, $field, $storeId = null , $website = null, $inherit = false)
    {
        $this->getMagentoConfig()
            ->setSection( $this->_getSection() )
            ->setWebsite( $website )
            ->setStore( $storeId )
            ->setGroups(
                array(
                     $this->_getGroup() => array(
                        'fields' => array(
                            $field => array(
                                'value' => $value,
                                'inherit' => $inherit
                            )
                        )
                    )
                )
            )->save();

        return $this;
    }

    protected function _unserialize( $value )
    {
        return $this->brightpearlConfigBackendHelper->unserialize( $value );
    }

}