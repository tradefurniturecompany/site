<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Order extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $transactionStatusGetFactory;
    protected $brightpearlPlatformDataFactory;
    protected $transactionFieldMetadataGetFactory;
    protected $transactionOrderGetFactory;
    protected $transactionCreditPostFactory;
    protected $transactionCreditGetFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Status\GetFactory $transactionStatusGetFactory,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $brightpearlPlatformDataFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Field\Metadata\GetFactory $transactionFieldMetadataGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Order\GetFactory $transactionOrderGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Credit\PostFactory $transactionCreditPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Credit\GetFactory $transactionCreditGetFactory
    )
    {
        $this->transactionStatusGetFactory = $transactionStatusGetFactory;
        $this->brightpearlPlatformDataFactory = $brightpearlPlatformDataFactory;
        $this->transactionFieldMetadataGetFactory = $transactionFieldMetadataGetFactory;
        $this->transactionOrderGetFactory = $transactionOrderGetFactory;
        $this->transactionCreditPostFactory = $transactionCreditPostFactory;
        $this->transactionCreditGetFactory = $transactionCreditGetFactory;
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
        return 'Order API';
    }

    public function getStatuses( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionStatusGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $orderStatuses = array();
        foreach ($response->getStatuses() as $status) {
            $orderStatuses[] = $this->brightpearlPlatformDataFactory->create()->map($status);
        }
        return $orderStatuses;
    }

    public function getCustomFields($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionFieldMetadataGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode)
            ->setSource('sale');

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $fields = array();
        foreach ($response->getFieldsMetadata() as $meta) {
            $fields[] = $this->brightpearlPlatformDataFactory->create()->map($meta);
        }
        return $fields;
    }

    /**
     * @deprecated
     */
    public function getOrder( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode );

        $transaction = $this->transactionOrderGetFactory->create();
        $transaction
            ->setIdSet( $idSet )
            ->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

        return $this->brightpearlPlatformDataFactory->create()->map( $response->getOrder() );
    }

    public function getOrders($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode );

        $transaction = $this->transactionOrderGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));
        $orders   = ( $orders = $response->getOrders() ) ? $orders : array();
        $bpIntegrationInstanceId = $response->getHeader('brightpearl-installed-integration-instance-id')->getFieldValue();

        $_rejected = array();
        $_orders   = array();
        foreach ( $orders as $orderId => $order ) {

            if ( isset($order['installedIntegrationInstanceId']) and
                 $order['installedIntegrationInstanceId'] == $bpIntegrationInstanceId ) {
                $_orders[ $order[ 'id' ] ] = $this->brightpearlPlatformDataFactory->create()->map($order);
            }
            else {
                $_rejected[] = $order['id'];
            }
        }

        if ( $_rejected ) {
            $this->getReport()->debug( 'Order id(s) rejected due to integration-instance-id mismatch: ['.implode(',', $_rejected).']' );
        }

        return $_orders;
    }

    public function exportCredit( $storeId, $accountCode, \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\Export $credit, $timeout = 5000 )
    {
        $report = $this->getReport();
        $this
            ->_assertNotEmpty( 'storeId',     $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode )
            ->_assertNotEmpty( 'credit',      $credit );

        $transaction = $this->transactionCreditPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setCredit( $credit );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        return $response;
    }

    public function getSalesCredits( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode );

        $transaction = $this->transactionCreditGetFactory->create();
        $transaction
            ->setIdSet( $idSet )
            ->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        $salesCredits = array();
        foreach( $response->getSalesCredits() as $salesCredit )
            {
                $salesCredits[] = $this->brightpearlPlatformDataFactory->create()->map( $salesCredit );
            }
        return $salesCredits;
    }

}