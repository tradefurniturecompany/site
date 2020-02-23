<?php
namespace Hotlink\Brightpearl\Helper\Api;

class Authorisation extends \Hotlink\Brightpearl\Helper\Api\AbstractApi
{

    protected $apiTransactionFactory;
    protected $brightpearlConfigAuthorisation;
    protected $apiTransportFactory;

    function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,

        \Hotlink\Brightpearl\Model\Api\Authorisation\Transaction\AuthoriseFactory $apiTransactionFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Api\Authorisation\TransportFactory $apiTransportFactory
    )
    {
        parent::__construct( $exceptionHelper, $reportHelper, $brightpearlConfigApi );
        $this->apiTransactionFactory = $apiTransactionFactory;
        $this->brightpearlConfigAuthorisation = $brightpearlConfigAuthorisation;
        $this->apiTransportFactory = $apiTransportFactory;
    }

    function getName()
    {
        return 'Authorisation API';
    }

    function requestAccessToken($storeId, $accountCode, $requestToken, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('requestToken', $requestToken);

        $transaction = $this->apiTransactionFactory->create()
            ->setAccountCode($accountCode)
            ->setRequestToken($requestToken);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        return $response->getInstanceToken();
    }

    protected function _getTransport($storeId = null, $timeout = null)
    {
        $config    = $this->brightpearlConfigAuthorisation;
        $transport = $this->apiTransportFactory->create()
                   ->setBaseUrl( $config->getApiDomain($storeId) );

        if (!is_null($timeout))
            $transport->setTimeout($timeout);

        return $transport;
    }

}
