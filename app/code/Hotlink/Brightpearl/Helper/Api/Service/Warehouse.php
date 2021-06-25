<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Warehouse extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $transactionWarehouseGetFactory;
    protected $transactionWarehouseLocationQuarantineGetFactory;

    protected $platformDataBrightpearlWarehouseFactory;
    protected $transactionShippingGetFactory;
    protected $brightpearlPlatformDataFactory;
    protected $transactionGoodsoutGetFactory;
    protected $transactionDropshipGetFactory;
    protected $transactionGoodsinPostFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Warehouse\GetFactory $transactionWarehouseGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Warehouse\Location\Quarantine\GetFactory $transactionWarehouseLocationQuarantineGetFactory,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\WarehouseFactory $platformDataBrightpearlWarehouseFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Shipping\GetFactory $transactionShippingGetFactory,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $brightpearlPlatformDataFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsout\GetFactory $transactionGoodsoutGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Dropship\GetFactory $transactionDropshipGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Warehouse\Transaction\Goodsin\PostFactory $transactionGoodsinPostFactory
    )
    {
        $this->transactionWarehouseGetFactory = $transactionWarehouseGetFactory;
        $this->transactionWarehouseLocationQuarantineGetFactory = $transactionWarehouseLocationQuarantineGetFactory;

        $this->platformDataBrightpearlWarehouseFactory = $platformDataBrightpearlWarehouseFactory;
        $this->transactionShippingGetFactory = $transactionShippingGetFactory;
        $this->brightpearlPlatformDataFactory = $brightpearlPlatformDataFactory;
        $this->transactionGoodsoutGetFactory = $transactionGoodsoutGetFactory;
        $this->transactionDropshipGetFactory = $transactionDropshipGetFactory;
        $this->transactionGoodsinPostFactory = $transactionGoodsinPostFactory;
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
        return 'Warehouse API';
    }

    public function getWarehouses($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionWarehouseGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $warehouses = array();
        foreach ( $response->getWarehouses() as $warehouse ) {
            $warehouses[] = $this->platformDataBrightpearlWarehouseFactory->create()->map($warehouse);
        }
        return $warehouses;
    }

    public function getShippingMethods($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionShippingGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $methods = array();
        foreach ($response->getMethods() as $method) {
            $methods[] = $this->brightpearlPlatformDataFactory->create()->map($method);
        }
        return $methods;
    }

    public function getGoodsoutNote($storeId, $accountCode, $idOrderSet = '*', $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionGoodsoutGetFactory->create();
        $transaction
            ->setOrderIdSet($idOrderSet)
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));
        $notes    = $response->getNotes();

        $_notes = array();
        foreach ($notes as $_noteId => $_noteInfo) {
            $_notes[$_noteId] = $this->brightpearlPlatformDataFactory->create()->map($_noteInfo);
        }
        return $_notes;
    }

    public function getDropshipNote($storeId, $accountCode, $idOrderSet = '*', $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionDropshipGetFactory->create();
        $transaction
            ->setOrderIdSet($idOrderSet)
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $result = null;
        try {

            $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));
            $result   = $response->getNotes();
        }
        catch (\Hotlink\Framework\Model\Exception\Transport $e) {
            if ( $e->getCode() != \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse::CODE_NOT_FOUND ) {
                throw $e;
            }
        }
        $notes = !is_null($result) ? $result : array();

        $_notes = array();
        foreach ($notes as $_noteId => $_noteInfo) {
            $_notes[$_noteId] = $this->brightpearlPlatformDataFactory->create()->map($_noteInfo);
        }
        return $_notes;
    }

    public function exportGoodsinNote( $storeId, $accountCode, $purchaseOrderId, $note, $timeout = 5000 )
    {
        $report = $this->getReport();
        $this
            ->_assertNotEmpty( 'storeId',         $storeId )
            ->_assertNotEmpty( 'accountCode',     $accountCode )
            ->_assertNotEmpty( 'purchaseOrderId', $purchaseOrderId )
            ->_assertNotEmpty( 'note',            $note );

        $transaction = $this->transactionGoodsinPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setPurchaseOrderId( $purchaseOrderId )
            ->setNote( $note );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        return $response;
    }

    public function getWarehouseLocationQuarantine( $storeId, $accountCode, $idSet, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode )
            ->_assertNotEmpty( 'idSet', $idSet );

        $transaction = $this->transactionWarehouseLocationQuarantineGetFactory->create();
        $transaction
            ->setIdSet( $idSet )
            ->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        return $response->getLocation();
    }

}