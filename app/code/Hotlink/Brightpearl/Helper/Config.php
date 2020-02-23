<?php
namespace Hotlink\Brightpearl\Helper;

class Config
{

    protected $magentoConfigFactory;
    protected $magentoConfig;

    function __construct( \Magento\Config\Model\Config\Factory $magentoConfigFactory )
    {
        $this->magentoConfigFactory = $magentoConfigFactory;
    }

    protected function getMagentoConfig()
    {
        if ( !$this->magentoConfig )
            {
                $this->magentoConfig = $this->magentoConfigFactory->create();
            }
        return $this->magentoConfig;
    }

    function saveValue( $section, $group, $value, $field, $storeId = null , $website = null, $inherit = false )
    {
        $this->getMagentoConfig()
            ->setSection( $section )
            ->setWebsite( $website )
            ->setStore( $storeId )
            ->setGroups(
                array(
                    $group => array(
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

}
