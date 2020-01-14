<?php
namespace Hotlink\Brightpearl\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $storeManager;
    protected $brightpearlExceptionHelper;
    protected $urlBuilder;
    protected $httpAuth;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Url $urlBuilder,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper,
        \Hotlink\Brightpearl\Helper\Http\Auth $httpAuth
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;
        $this->httpAuth = $httpAuth;

        parent::__construct( $context );
    }

    public function getSerializedOptions( $value )
    {
        return serialize( $value );
    }

    public function getUnserializedOptions( $value )
    {
        $arr = @unserialize( $value );
        if ( !is_array( $arr ) ) {
            return '';
        }

        $sortOrder = array();
        foreach ( $arr as $k => $val ) {
            if ( !is_array( $val ) ) {
                unset( $arr[ $k ] );
                continue;
            }
        }
        return $arr;
    }

    public function getBaseCallbackUrl($urlRoute, $options = array())
    {
        $options['_secure'] = true;
        $options['_nosid'] = true;

        $storeInUrl = $this->scopeConfig->getValue( \Magento\Store\Model\Store::XML_PATH_STORE_IN_URL,
                                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                                    $this->storeManager->getStore()->getCode() );
        if ( $storeInUrl ) {
            // when store code in url is ON a store code (any) has to be in callback url,
            // otherwise magento 404s the callback url

            $storeId = null;
            foreach ($this->storeManager->getWebsites() as $website) {
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    foreach ($stores as $store) {
                        if ($store->getIsActive()) {
                            $storeId = $store->getId();
                            break 3;
                        }
                    }
                }
            }

            if (is_null($storeId)) {
                $this->brightpearlExceptionHelper->throwValidation('Unable to find an Enabled store to use in callback url');
            }

            $options['_store'] = $storeId;
        }

        $url = $this->urlBuilder->getUrl( $urlRoute, $options );
        $url = $this->httpAuth->getUrl( $url, $storeInUrl );
        return $url;
    }

}
