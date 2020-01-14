<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Authorisation;

class Locate extends \Hotlink\Brightpearl\Controller\Adminhtml\Authorisation\AbstractAuthorisation
{

    protected $storeManager;
    protected $authorisationConfig;
    protected $utilityApi;
    protected $exceptionHelper;
    protected $brightpearlHelper;
    protected $httpAuthHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\ReportFactory $interactionReportFactory,
        \Hotlink\Framework\Model\UserFactory $interactionUserFactory,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Brightpearl\Model\Config\Authorisation $authorisationConfig,
        \Hotlink\Brightpearl\Helper\Api\Utility $utilityApi,
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper,
        \Hotlink\Brightpearl\Helper\Http\Auth $httpAuthHelper
    )
    {
        $this->storeManager = $storeManager;
        $this->authorisationConfig = $authorisationConfig;
        $this->utilityApi = $utilityApi;
        $this->exceptionHelper = $exceptionHelper;
        $this->brightpearlHelper = $brightpearlHelper;
        $this->httpAuthHelper = $httpAuthHelper;

        parent::__construct(
            $context,
            $interactionReportFactory,
            $interactionUserFactory
        );
    }

    public function execute()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $config = $this->authorisationConfig;

        $report = $this->_initReport();
        $report
            ->setProcess('Authorisation Locate Account')
            ->setTrigger('Admin user request')
            ->setContext('On Admin authorise')
            ->info('Starting Authorisation Locate Account');

        $accountCode = trim($this->getRequest()->getParam('accountCode'));
        if (!$accountCode) {
            $this->messageManager->addError( __('[accountCode] parameter is required') );
            $report->error('[accountCode] parameter is required');

            return $this->redirect( '*/*/form' );
        }

        $report
            ->addReference($accountCode)
            ->debug('account code = '.$accountCode);

        // 2. Init parameters for transaction
        $devRef = \Hotlink\Brightpearl\Model\Platform::DEV_REF;
        $appRef = \Hotlink\Brightpearl\Model\Platform::APP_REF_M2;

        try {
            // 3. VALIDATE account with BP
            $report
                ->info('Validating account code with BP')
                ->indent();

            $locationResponse = $report( $this->utilityApi,
                                         'locateAccount',
                                         $storeId,
                                         $accountCode,
                                         $devRef );

            $authorizeInstanceUrl = isset($locationResponse[0]) ? $locationResponse[0] : null;

            if (!$authorizeInstanceUrl) {
                $this->exceptionHelper->throwAuthorisation( 'Unable to retrieve [response.urls.authorizeInstance]' );
            }

            $report->unindent()
                ->info('Saving data in config')
                ->indent();

            // 0. Security measure: generate and save OTP to pass to Brightpearl within callbackUrl, to
            //    prevent unauthorised attempts to invoke callback as that is a frontend controller action
            $otp = md5( uniqid( rand() ) );
            $report->debug('OTP = '.$otp);
            $config
                ->saveOtp( $otp )
                ->saveAccountCode( $accountCode  );

            $apiDomain = isset($locationResponse[1]) ? $locationResponse[1] : null;
            if (!$apiDomain) {
                $this->exceptionHelper->throwAuthorisation('Unable to retrieve [response.apiDomain]' );
            }

            // save api to be used with all future api requests
            $config->saveApiDomain( $apiDomain );
            $report->debug('user api domain = '.$apiDomain)->unindent();

            // 4. redirect user to Authentication Page on BP
            $report
                ->debug('devRef = '.$devRef)
                ->debug('appRef = '.$appRef);

            $returnUrl = $this->urlBuilder->getUrl( '*/*/returnaction' ); // not URLENCODEd as seems to break BP authorisation
            $returnUrl = $this->httpAuthHelper->getUrl( $returnUrl, $storeId );

            $callbackUrl = $this->brightpearlHelper->getBaseCallbackUrl( 'hotlink_brightpearl/authorisation/callback',
                                                                         array( '_query' => array('otp' => $otp) ) );

            $bpAuthoriseUrl = sprintf(
                '%s?devRef=%s&appRef=%s&returnUrl=%s&callbackUrl=%s&accountCode=%s&description=%s',
                $authorizeInstanceUrl,
                urlencode( $devRef ),
                urlencode( $appRef ),
                $returnUrl,
                $callbackUrl,   // frontend URL
                urlencode( $accountCode ),
                urlencode( 'Hotlink Brightpearl Magento integration' ));

            $report
                ->info('Redirecting user to Brightpearl')
                ->indent()
                ->debug('redirect url = '.$bpAuthoriseUrl)
                ->unindent()
                ->setStatus(\Hotlink\Framework\Model\Report::STATUS_SUCCESS)
                ->close();

            return $this->redirect( $bpAuthoriseUrl, false );
        }
        catch ( \Exception $e ) {
            $this->messageManager
                ->addError( 'Unable to verify account code with Brightpearl. Please try again!' )
                ->addError( $e->getMessage() );

            $report
                ->error( $e->getMessage() )
                ->setStatus(\Hotlink\Framework\Model\Report::STATUS_EXCEPTION)
                ->close();

            return $this->redirect( '*/*/form' );
        }
    }

}
