<?php
namespace Hotlink\Brightpearl\Controller\OAuth2;

class Callback extends \Magento\Framework\App\Action\Action
{

    protected $storeManager;
    protected $authorisationConfig;
    protected $brightpearlExceptionHelper;
    protected $brightpearlApiAuthorisationHelper;
    protected $dateTimeDateTimeFactory;
    protected $logger;
    protected $interactionReportFactory;
    protected $interactionUserFactory;
    protected $brightpearlHelper;
    protected $productMetadata;
    protected $resultJsonFactory;

    protected $oauth2otp;

    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeDateTimeFactory,
        \Psr\Log\LoggerInterface $logger,

        \Magento\Framework\App\ProductMetadataInterface $productMetadata,

        \Hotlink\Framework\Model\Platform $hotlinkPlatform,
        \Hotlink\Brightpearl\Model\Platform $brightpearlPlatform,
        
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Model\UserFactory $interactionUserFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $authorisationConfig,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper,
        \Hotlink\Brightpearl\Helper\Api\Authorisation $brightpearlApiAuthorisationHelper,
        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper,

        \Hotlink\Brightpearl\Helper\Api\OAuth2\Otp $oauth2otp

    ) {
        $this->storeManager = $storeManager;
        $this->authorisationConfig = $authorisationConfig;
        $this->brightpearlExceptionHelper = $brightpearlExceptionHelper;
        $this->brightpearlApiAuthorisationHelper = $brightpearlApiAuthorisationHelper;
        $this->dateTimeDateTimeFactory = $dateTimeDateTimeFactory;
        $this->logger = $logger;
        $this->interactionReportFactory = $interactionReportFactory;
        $this->interactionUserFactory = $interactionUserFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->oauth2otp = $oauth2otp;
        $this->productMetadata = $productMetadata;
        $this->hotlinkPlatform = $hotlinkPlatform;
        $this->brightpearlPlatform = $brightpearlPlatform;
        $this->brightpearlHelper = $brightpearlHelper;

        parent::__construct(
            $context
        );
    }

    function execute()
    {
        $result = $this->resultJsonFactory->create();

        $report = $this->_initReport();
        $report
            ->setProcess( 'OAuth2 Url Validation' )
            ->setTrigger( 'OAuth2 Proxy callback' )
            ->setContext( 'On Brightpearl proxy callback detected' )
            ->info( 'Starting OAuth Callback' )
            ->indent();

        try
            {
                $otpParam = trim( $this->getRequest()->getParam( 'otp' ) );

                if ( !$otpParam )
                    {
                        $report
                            ->error( "no otp provided" )
                            ->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
                        $this->brightpearlExceptionHelper->throwAuthorisation( 'Missing OTP' );  // Http 403
                    }

                $otp = $this->oauth2otp->get();
                if ( $otp == $otpParam )
                    {
                        $report->addReference( $otpParam );
                    }
                else
                    {
                        $report
                            ->addReference( $otpParam, $otp )
                            ->error( "otp mismatch ( received, expecting ) : ( $otpParam, $otp )" )
                            ->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
                        $this->brightpearlExceptionHelper->throwAuthorisation( 'Invalid OTP: expected [%1] but given [%2]', $otp, $otpParam );  // Http 403
                    }

                $report->debug( "otp = $otp" );

                if ( $this->oauth2otp->isExpired() )
                    {
                        $report
                            ->error( "otp expired" )
                            ->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
                        $this->brightpearlExceptionHelper->throwAuthorisation( "OTP Expired" );  // Http 403
                    }

                $this->oauth2otp->invalidate();

                $magento = $this->productMetadata->getName() . '/'.$this->productMetadata->getVersion() . ' (' . $this->productMetadata->getEdition() . ')';
                $hotlink = $this->hotlinkPlatform->getModuleVersion();
                $brightpearl = $this->brightpearlPlatform->getModuleVersion();
                $appref = \Hotlink\Brightpearl\Model\Platform::APP_REF_M2;

                $redirectUrl = $this->oauth2otp->getSuccessUrl();

                $report
                    ->info( "Callback successful" )
                    ->debug( "magento = $magento" )
                    ->debug( "brightpearl = $brightpearl" )
                    ->debug( "hotlink = $hotlink" )
                    ->debug( "appref = $appref" )
                    ->debug( "redirectUrl = $redirectUrl" )
                    ->setStatus( \Hotlink\Framework\Model\Report::STATUS_SUCCESS );
                $result->setData( [ "magento"          => $magento,
                                    "brightpearl"      => $brightpearl,
                                    "hotlink"          => $hotlink,
                                    "appref"           => $appref,
                                    "success_redirect" => $redirectUrl
                ] );
            }
        catch ( \Hotlink\Brightpearl\Model\Exception\Authorisation $e )
            {
                $report->error( $e->getMessage() );
                $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );

                $result->setHttpResponseCode( \Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN );
                $result->setData( [ "error" => "Invalid OTP" ] );
            }
        catch ( \Exception $e )
            {
                $this->logger->critical( $e );

                $report->error( $e->getMessage() );
                $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );

                $result->setHttpResponseCode( \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR );
                $result->setData( [ 'error' => $e->getMessage() ] );
            }
        $report->close();

        return $result;
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