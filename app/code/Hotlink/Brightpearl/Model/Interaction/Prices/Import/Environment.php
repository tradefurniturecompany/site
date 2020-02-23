<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Shared\Price
     */
    protected $brightpearlConfigSharedPrice;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment $htmlFormEnvironmentHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Config\Shared\Price $brightpearlConfigSharedPrice,
        $storeId
    ) {
        $this->brightpearlConfigSharedPrice = $brightpearlConfigSharedPrice;

        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $htmlFormEnvironmentHelper,
            $storeManager,
            $interaction,
            $brightpearlConfigAuthorisation,
            $brightpearlConfigOAuth2,
            $storeId );
    }

    protected function _getParameterModels()
    {
        return [
            '\Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Websites',
            '\Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skus',
            '\Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip\Attribute',
            '\Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment\Skip\Tier' ];
    }

    function getProductTypes()
    {
        return $this->brightpearlConfigSharedPrice->getProductType( $this->getStoreId() );
    }

    function getBasePriceList()
    {
        return $this->brightpearlConfigSharedPrice->getBasePriceList( $this->getStoreId() );
    }

    function getPriceAttributeMapping()
    {
        return $this->brightpearlConfigSharedPrice->getPriceAttributeMapping( $this->getStoreId() );
    }

    function getCustomerGroupPriceListMap()
    {
        return $this->brightpearlConfigSharedPrice->getCustomerGroupPriceListMap( $this->getStoreId() );
    }

    function getTierPriceListMap( $websiteId = 0 )
    {
        $result = array();
        $tierPriceMapping = $this->brightpearlConfigSharedPrice->getTierPriceListMap( $this->getStoreId() );
        foreach ( $tierPriceMapping as $key => $map )
            {
                if ( $websiteId == $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::WEBSITE ] )
                    {
                        $result[ $key ] = $map;
                    }
            }
        return $result;
    }

    function getBatch()
    {
        return $this->getConfig()->getBatch( $this->getStoreId() );
    }

    function getSleep()
    {
        return $this->getConfig()->getSleep( $this->getStoreId() );
    }

    function getCheckTaxCompatibility()
    {
        return $this->getConfig()->getCheckTaxCompatibility( $this->getStoreId() );
    }

}
