<?php
namespace Hotlink\Brightpearl\Model\Interaction\Environment;

abstract class AbstractEnvironment extends \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment
{

    protected $brightpearlConfigAuthorisation;
    protected $brightpearlConfigOAuth2;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment $htmlFormEnvironmentHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,

        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        $storeId
    )
    {
        parent::__construct( $exceptionHelper,
                             $reflectionHelper,
                             $reportHelper,
                             $factoryHelper,
                             $htmlFormEnvironmentHelper,
                             $storeManager,
                             $interaction,
                             $storeId );

        $this->brightpearlConfigAuthorisation = $brightpearlConfigAuthorisation;
        $this->brightpearlConfigOAuth2 = $brightpearlConfigOAuth2;
    }

    public function isOAuth2Active()
    {
        return $this->brightpearlConfigOAuth2->isActive();
    }

    //
    //  General
    //
    public function getApiTimeout( $storeId = null )
    {
        return $this->getConfig()->getApiTimeout( $storeId );
    }

    public function getApiQueryLimit( $storeId = null )
    {
        return $this->_getApiConfig()->getQueryLimit( $storeId );
    }

    protected function _getApiConfig()
    {
        return $this->factory()->get( '\Hotlink\Brightpearl\Model\Config\Api' );
    }

    //
    //  Automatic OAuth or Legacy
    //
    public function getAccountCode()
    {
        return $this->isOAuth2Active()
            ? $this->getOAuth2AccountCode()
            : $this->getLegacyAccountCode();
    }

    public function getAuthToken()
    {
        return $this->isOAuth2Active()
            ? $this->getOAuth2Token()
            : $this->getLegacyToken();
    }

    //
    //  OAuth2
    //
    public function getOAuth2AccountCode()
    {
        return $this->brightpearlConfigOAuth2->getAccount( $this->getStoreId() );
    }

    public function getOAuth2Token()
    {
        return $this->brightpearlConfigOAuth2->getAccessToken( $this->getStoreId() );
    }

    public function getOAuth2InstanceId()
    {
        return $this->brightpearlConfigOAuth2->getInstallationInstanceId( $this->getStoreId() );
    }

    //
    //  Legacy
    //
    public function getLegacyAccountCode()
    {
        return $this->brightpearlConfigAuthorisation->getAccountCode( $this->getStoreId() );
    }

    public function getLegacyToken()
    {
        return $this->brightpearlConfigAuthorisation->getToken( $this->getStoreId() );
    }

}
