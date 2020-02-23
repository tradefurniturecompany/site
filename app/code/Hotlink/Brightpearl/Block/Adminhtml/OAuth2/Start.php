<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\OAuth2;

class Start extends \Magento\Framework\View\Element\Template
{

    protected $oauth2otp;
    protected $oauth2config;
    protected $brightpearlHelper;
    protected $backendHelper;

    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        \Magento\Backend\Helper\Data $backendHelper,
        
        \Hotlink\Brightpearl\Helper\Api\OAuth2\Otp $oauth2otp,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $oauth2config,
        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper,


        array $data = []
    )
    {
        $this->oauth2otp = $oauth2otp;
        $this->oauth2config = $oauth2config;
        $this->brightpearlHelper = $brightpearlHelper;
        $this->backendHelper = $backendHelper;
        
        parent::__construct( $context, $data );

    }

    protected function _prepareLayout()
    {
        // Each reload of the page generates a new OTP (whether it has expired or not)
        $otp = $this->oauth2otp->create();

        // The final url to use (must utilise same admin key as current user)
        // If session code is not enabled in the url key, then the cookie will work for us
        // We set this here because the session key may be included in the url - this cannot be achieved reliably within
        // the frontend controller callback, as the request will not have admin access permissions (cookie or other).

        $successUrl = $this->backendHelper->getUrl( 'hotlink_brightpearl/oauth2/confirm' );  // No more OTP


        // To be copy and pasted into Proxy UI
        $callbackUrl = $this->brightpearlHelper->getBaseCallbackUrl( 'hotlink_brightpearl/oauth2/callback',
                                                                     array( '_query' => array( 'otp' => $otp ) ) );

        $this->oauth2otp->save( $otp, $callbackUrl, $successUrl );

        $this->setCallbackUrl( $callbackUrl );

        $this->setProxyUrl( $this->oauth2config->getProxyUrl() );
        return parent::_prepareLayout();
    }

}
