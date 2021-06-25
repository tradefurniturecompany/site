<?php
namespace Hotlink\Brightpearl\Helper\Api;

class Utility extends \Hotlink\Brightpearl\Helper\Api\AbstractApi
{

    protected $apiTransactionLocateFactory;
    protected $brightpearlConfigAuthorisation;
    protected $apiTransportFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,

        \Hotlink\Brightpearl\Model\Api\Utility\Transaction\Account\LocateFactory $apiTransactionLocateFactory,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Api\TransportFactory $apiTransportFactory
    )
    {
        parent::__construct( $exceptionHelper, $reportHelper, $brightpearlConfigApi );
        $this->apiTransactionLocateFactory = $apiTransactionLocateFactory;
        $this->brightpearlConfigAuthorisation = $brightpearlConfigAuthorisation;
        $this->apiTransportFactory = $apiTransportFactory;
    }

    public function getName()
    {
        return 'Utility API';
    }

    public function locateAccount($storeId, $accountCode, $devRef, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('devRef', $devRef);

        $transaction = $this->apiTransactionLocateFactory->create()
                     ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        return array($response->getAuthorizeInstanceUrl(),
                     $response->getApiDomain());
    }

    protected function _getTransport($storeId = null, $timeout = null)
    {
        $config    = $this->brightpearlConfigAuthorisation;
        $transport = $this->apiTransportFactory->create();
        $transport->setBaseUrl( $config->getLocationUrl($storeId) );

        if (!is_null($timeout))
            $transport->setTimeout($timeout);

        return $transport;
    }

}