<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\OAuth2;

class Finish extends \Hotlink\Brightpearl\Controller\Adminhtml\OAuth2\AbstractOAuth2
{

    protected $interactionReportFactory;
    protected $interactionUserFactory;
    protected $apiOAuth2Helper;
    protected $apiOAuth2OtpHelper;
    protected $configOAuth2;
    protected $brightpearlHelper;

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Model\UserFactory $interactionUserFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Hotlink\Brightpearl\Helper\Api\OAuth2 $apiOAuth2Helper,
        \Hotlink\Brightpearl\Helper\Api\OAuth2\Otp $apiOAuth2OtpHelper,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $configOAuth2,
        
        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper
    )
    {
        $this->interactionReportFactory = $interactionReportFactory;
        $this->interactionUserFactory = $interactionUserFactory;
        $this->apiOAuth2Helper = $apiOAuth2Helper;
        $this->apiOAuth2OtpHelper = $apiOAuth2OtpHelper;
        $this->configOAuth2 = $configOAuth2;
        $this->brightpearlHelper = $brightpearlHelper;
        parent::__construct( $context, $resultPageFactory );
    }

    function execute()
    {
        $request = $this->getRequest();
        if ( $request->isPost() )
            {
                $account = $request->getParam( 'account' );
                $code = $request->getParam( 'code' );
                if ( $account && $code )
                    {
                        $report = $this->_initReport();
                        $report
                            ->setProcess( 'OAuth2 Token Acquisition' )
                            ->setTrigger( 'Authentication Approval' )
                            ->setContext( 'On User Approved Authentication within Magento' )
                            ->info( 'Obtaining token' )
                            ->indent();

                        // obtain a token by submitting the code to the api
                        $storeId = \Magento\Store\Model\Store::ADMIN_CODE;
                        $clientId = $this->apiOAuth2OtpHelper->getCallbackUrl();
                        $redirectUri = $clientId;
                        $response = $report( $this->apiOAuth2Helper, 'requestAccessToken',
                                             $storeId, $account, $code, $redirectUri, $clientId );

                        $accessToken = $response->getAccessToken();
                        $tokenType = $response->getTokenType();
                        $expiresIn = $response->getExpiresIn();
                        $refreshToken = $response->getRefreshToken();
                        $installationInstanceId = $response->getInstallationInstanceId();
                        $apiDomain = $response->getApiDomain();

                        // save the response data
                        $this->configOAuth2->saveAccount( $account );
                        $this->configOAuth2->saveAccessToken( $accessToken );
                        $this->configOAuth2->saveExpiresIn( $expiresIn );
                        $this->configOAuth2->saveRefreshToken( $refreshToken );
                        $this->configOAuth2->saveInstallationInstanceId( $installationInstanceId );
                        $this->configOAuth2->saveApiDomain( $apiDomain );

                        $report->unindent();
                        $report->close();
                    }
            }
        else
            {
                $this->getResponse()->setRedirect( $this->getUrl( '*/template' ) );
                return;
            }
        return parent::execute();
    }

    protected function _initReport()
    {
        $report = $this->interactionReportFactory->create( [ 'object' => $this ] );
        $report
            ->setUser( $this->interactionUserFactory->create()->getDescription() )
            ->addLogWriter()
            ->addItemWriter()
            ->addDataWriter();
        return $report;
    }

}
