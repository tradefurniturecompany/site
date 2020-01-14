<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Integration extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $transactionWebhookGetFactory;
    protected $transactionWebhookPostFactory;
    protected $transactionWebhookDeleteFactory;
    protected $transactionInstanceGetFactory;
    protected $transactionConfigurationGetFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook\GetFactory $transactionWebhookGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook\PostFactory $transactionWebhookPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Webhook\DeleteFactory $transactionWebhookDeleteFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Instance\GetFactory $transactionInstanceGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Integration\Transaction\Configuration\GetFactory $transactionConfigurationGetFactory
    ) {
        $this->transactionWebhookGetFactory = $transactionWebhookGetFactory;
        $this->transactionWebhookPostFactory = $transactionWebhookPostFactory;
        $this->transactionWebhookDeleteFactory = $transactionWebhookDeleteFactory;
        $this->transactionInstanceGetFactory = $transactionInstanceGetFactory;
        $this->transactionConfigurationGetFactory = $transactionConfigurationGetFactory;
        parent::__construct(
            $exceptionHelper,
            $reportHelper,
            $brightpearlConfigApi,
            $brightpearlConfigAuthorisation,
            $brightpearlConfigOAuth2,
            $brightpearlApiServiceTransportFactory
        );
    }

    public function getName()
    {
        return 'Integration API';
    }

    public function getWebhook( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionWebhookGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        try {
            $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));
        }
        catch (\Hotlink\Framework\Model\Exception\Transport $e) {

            if ($e->getCode() == \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse::CODE_NOT_FOUND) {
                return null;
            }
            else {
                throw $e;
            }
        }

        return $response->getWebhooks();
    }

    public function createWebhook( $storeId,
                                   $accountCode,
                                   $subscribeTo,
                                   $httpMethod,
                                   $uriTemplate,
                                   $bodyTemplate,
                                   $contentType,
                                   $idSetAccepted,
                                   $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('subscribeTo', $subscribeTo)
            ->_assertNotEmpty('httpMethod', $httpMethod)
            ->_assertNotEmpty('uriTemplate', $uriTemplate)
            ->_assertNotEmpty('contentType', $contentType);

        $transaction = $this->transactionWebhookPostFactory->create();
        $transaction
            ->setAccountCode($accountCode)
            ->setSubscribeTo($subscribeTo)
            ->setHttpMethod($httpMethod)
            ->setUriTemplate($uriTemplate)
            ->setBodyTemplate($bodyTemplate)
            ->setContentType($contentType)
            ->setIdSetAccepted((bool)$idSetAccepted);

        $response = $this->submit($transaction, $this->_getTransport($storeId));

        return $response->getWebhookId();
    }

    public function deleteWebhook( $storeId, $accountCode, $webhookId, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('webhookId', $webhookId);

        $transaction = $this->transactionWebhookDeleteFactory->create();
        $transaction
            ->setAccountCode($accountCode)
            ->setWebhookId($webhookId);

        return $this->submit($transaction, $this->_getTransport($storeId, $timeout));
    }

    public function getInstance( $storeId, $accountCode, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId )
            ->_assertNotEmpty('accountCode', $accountCode );

        $transaction = $this->transactionInstanceGetFactory->create();
        $transaction->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );
        return $response->getInstance();
    }

    public function getConfiguration( $storeId, $accountCode, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId )
            ->_assertNotEmpty('accountCode', $accountCode );

        $transaction = $this->transactionConfigurationGetFactory->create();
        $transaction->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );
        return $response;
    }

}
