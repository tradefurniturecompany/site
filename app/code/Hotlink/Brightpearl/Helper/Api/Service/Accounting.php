<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Accounting extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $transactionNominalCodeGetFactory;
    protected $brightpearlPlatformDataFactory;
    protected $transactionCustomerPaymentPostFactory;

    function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Accounting\Transaction\Nominal\Code\GetFactory $transactionNominalCodeGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Accounting\Transaction\Customer\Payment\PostFactory $transactionCustomerPaymentPostFactory,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $brightpearlPlatformDataFactory
    )
    {
        $this->transactionNominalCodeGetFactory = $transactionNominalCodeGetFactory;
        $this->transactionCustomerPaymentPostFactory = $transactionCustomerPaymentPostFactory;
        $this->brightpearlPlatformDataFactory = $brightpearlPlatformDataFactory;
        parent::__construct(
            $exceptionHelper,
            $reportHelper,
            $brightpearlConfigApi,
            $brightpearlConfigAuthorisation,
            $brightpearlConfigOAuth2,
            $brightpearlApiServiceTransportFactory
        );
    }

    function getName()
    {
        return 'Accounting API';
    }

    function getNominalCodes($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionNominalCodeGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $codes = array();
        foreach ($response->getCodes() as $code) {
            $codes[] = $this->brightpearlPlatformDataFactory->create()->map($code);
        }
        return $codes;
    }

    function exportRefund( $storeId, $accountCode, \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Refund\Export $refund, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode )
            ->_assertNotEmpty( 'refund', $refund );

        $transaction = $this->transactionCustomerPaymentPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setRefund( $refund->toArray() );

        $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

        return $response;
    }

}