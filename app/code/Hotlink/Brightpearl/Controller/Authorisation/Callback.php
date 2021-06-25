<?php
namespace Hotlink\Brightpearl\Controller\Authorisation;

class Callback extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Hotlink\Brightpearl\Model\Config\Authorisation
     */
    protected $authorisationConfig;

    /**
     * @var \Hotlink\Brightpearl\Helper\Exception
     */
    protected $brightpearlExceptionHelper;

    /**
     * @var \Hotlink\Brightpearl\Helper\Api\Authorisation
     */
    protected $brightpearlApiAuthorisationHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeDateTimeFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Hotlink\Framework\Model\ReportFactory
     */
    protected $interactionReportFactory;

    /**
     * @var \Hotlink\Framework\Model\UserFactory
     */
    protected $interactionUserFactory;

    protected $resultJsonFactory;

    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeDateTimeFactory,
        \Psr\Log\LoggerInterface $logger,
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Model\UserFactory $interactionUserFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $authorisationConfig,
        \Hotlink\Brightpearl\Helper\Exception $brightpearlExceptionHelper,
        \Hotlink\Brightpearl\Helper\Api\Authorisation $brightpearlApiAuthorisationHelper
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
        parent::__construct(
            $context
        );
    }

    function execute()
    {
        $result = $this->resultJsonFactory->create();

        $report = $this->_initReport();
        $report
            ->setProcess('Authorisation Callback')
            ->setTrigger('Brightpearl callback')
            ->setContext('On Brightpearl callback')
            ->info('Starting Authorisation Callback')
            ->indent();

        $requestToken = rawurldecode( trim( $this->getRequest()->getParam( 'requestToken' ) ) );
        $otpParam     = trim( $this->getRequest()->getParam('otp') );

        $report
            ->debug('requestToken = '.$requestToken)
            ->debug('otp = '.$otpParam)
            ->addReference( array($requestToken, $otpParam) );


        $storeId = $this->storeManager->getStore()->getId();
        $accountCode = $this->authorisationConfig->getAccountCode( $storeId );
        $otp         = $this->authorisationConfig->getOtp( $storeId );

        try {
            if ( !$requestToken ) {
                $this->brightpearlExceptionHelper->throwAuthorisation( "Required parameter [requestToken] missing" );
            }
            if ( !$otpParam ) {
                $this->brightpearlExceptionHelper->throwAuthorisation( 'Required parameter [otp] missing' );
            }

            // 0. Check provided OTP against saved value, as security measure
            if ( $otp !== $otpParam ) {
                $this->brightpearlExceptionHelper->throwAuthorisation( 'Supplied OTP is invalid: expected [%1] but given [%2]', $otp, $otpParam );
            }

            // 1. Use requestToken to retrieve the instance access token
            $report->info( 'Request Instance Access Token' );
            $accessToken = $report( $this->brightpearlApiAuthorisationHelper, 'requestAccessToken',
                                    $storeId,
                                    $accountCode,
                                    $requestToken);

            if ( !$accessToken ) {
                $this->brightpearlExceptionHelper->throwApi( 'Instance token API returned no token' );
            }

            $report
                ->info( 'Save token in config' )
                ->indent()
                ->debug( 'token = ' . $accessToken );

            // 2. Store access token for future use
            $this->authorisationConfig->saveToken( $accessToken );
            $this->authorisationConfig->saveTokenTimestamp(
                date('Y-m-d H:i:s', $this->dateTimeDateTimeFactory->create()->timestamp( time() ))
                );

            // 3. Provide SUCCESS response to BP server
            $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_SUCCESS );
            $result->setData( [] );
        }
        catch ( \Hotlink\Brightpearl\Model\Exception\Authorisation $e ) {
            $result->setHttpResponseCode( \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST );
            $result->setData( ["error" => $e->getMessage() ] );

            $report->error( $e->getMessage() );
            $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
        }
        catch ( \Exception $e ) {
            $this->logger->critical( $e );

            $result->setHttpResponseCode( \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR );
            $result->setData( ['error' => $e->getMessage()] );

            $report->error( $e->getMessage() );
            $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_EXCEPTION );
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