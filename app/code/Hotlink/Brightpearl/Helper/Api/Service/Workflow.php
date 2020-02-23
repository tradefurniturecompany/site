<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Workflow extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $productTransactionAvailabilityGetFactory;
    protected $dataBrightpearlStockListFactory;
    protected $orderTransactionPostFactory;
    protected $orderPaymentTransactionPostFactory;
    protected $orderStatusTransactionPostFactory;
    protected $productTransactionSkusPostFactory;
    protected $productTransactionPriceGetFactory;
    protected $productMessagePriceGetRequestFactory;
    protected $productMessageAvailabilityGetRequestFactory;
    protected $orderStatusTransactionGetFactory;
    protected $platformDataFactory;

    function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Availability\GetFactory $productTransactionAvailabilityGetFactory,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Stock\ListStockFactory $dataBrightpearlStockListFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Transaction\PostFactory $orderTransactionPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Payment\Transaction\PostFactory $orderPaymentTransactionPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Transaction\PostFactory $orderStatusTransactionPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Skus\PostFactory $productTransactionSkusPostFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Price\GetFactory $productTransactionPriceGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Price\Get\RequestFactory $productMessagePriceGetRequestFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Availability\Get\RequestFactory $productMessageAvailabilityGetRequestFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Transaction\GetFactory $orderStatusTransactionGetFactory,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $platformDataFactory

    ) {
        $this->productTransactionAvailabilityGetFactory = $productTransactionAvailabilityGetFactory;
        $this->dataBrightpearlStockListFactory = $dataBrightpearlStockListFactory;
        $this->orderTransactionPostFactory = $orderTransactionPostFactory;
        $this->orderPaymentTransactionPostFactory = $orderPaymentTransactionPostFactory;
        $this->orderStatusTransactionPostFactory = $orderStatusTransactionPostFactory;
        $this->productTransactionSkusPostFactory = $productTransactionSkusPostFactory;
        $this->productTransactionPriceGetFactory = $productTransactionPriceGetFactory;
        $this->productMessagePriceGetRequestFactory = $productMessagePriceGetRequestFactory;
        $this->productMessageAvailabilityGetRequestFactory = $productMessageAvailabilityGetRequestFactory;
        $this->orderStatusTransactionGetFactory = $orderStatusTransactionGetFactory;
        $this->platformDataFactory = $platformDataFactory;

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
        return 'Workflow Integration API';
    }

    function getProductAvailability($storeId, $accountCode, array $skus, array $warehouses, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('skus', $skus)
            ->_assertNotEmpty('warehouses', $warehouses);

        $transaction = $this->productTransactionAvailabilityGetFactory->create();
        $transaction
            ->setAccountCode($accountCode)
            ->setWarehouses($warehouses);

        $report     = $this->getReport();
        $transport  = $this->_getTransport($storeId, $timeout);
        $querylimit = $this->getApiConfig()->getQueryLimit();


        $request = $this->productMessageAvailabilityGetRequestFactory->create();

        $action = $request->buildAction($accountCode, $warehouses, []);
        if (strlen($action) > $querylimit) {
            $report->error('Unable to chunk skus for API request - query limit too low');
            return array();
        }

        // chunking
        $chunks = $rejected = array();

        $chunks = array();
        $accumulator = array();
        foreach ($skus as $_sku) {

            // try only with 1 sku
            $action = $request->buildAction($accountCode, $warehouses, [$_sku]);
            if (strlen($action) > $querylimit) {
                // too big to fit, even on its own
                $rejected[] = $_sku;
                continue;
            }

            $accumulator[] = $_sku;
            $action = $request->buildAction($accountCode, $warehouses, $accumulator);

            if (strlen($action) > $querylimit) {
                $last = array_pop($accumulator); // keep the overflow sku

                $chunks[]      = $accumulator; // keep the batch
                $accumulator   = array();      // reset
                $accumulator[] = $last;        // carry over the last sku
            }
        }
        if ( !empty( $accumulator ) ) {
            $chunks[] = $accumulator;
        }

        if ( !empty($rejected) ) {
            $report->debug( 'Skus rejected by the API ['.implode(",", $rejected).'] - too big to fit in the request.' );
        }

        $availability = array();
        foreach ($chunks as $_skus) {

            $transaction->setSkus( $_skus );
            $response = $this->submit($transaction, $transport);

            if ( $chunkAvailability = $response->getAvailability() ) {
                // NB: array_merge fails to preserve keys
                // http://stackoverflow.com/questions/12397563/array-merge-changes-the-keys
                // $availability = array_merge($availability, $chunkAvailability);
                $availability = $availability + $chunkAvailability;
            }
        }

        return $this->dataBrightpearlStockListFactory->create()->map( $availability );
    }

    function exportOrder($storeId, $accountCode, \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export $order, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('order', $order);

        $transaction = $this->orderTransactionPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setOrder( $order->toArray() );

        $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

        return $response;
    }

    function exportOrderPayment( $storeId, $accountCode, $orderIncrementId, \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Payments $payment, $timeout = 5000 )
    {
        $report = $this->getReport();
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('orderIncrementId', $orderIncrementId)
            ->_assertNotEmpty('payment', $payment);

        $transaction = $this->orderPaymentTransactionPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setOrderIncrementId( $orderIncrementId )
            ->setPayment( $payment->toArray() );

        $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

        return $response;
    }

    function exportOrderStatus( $storeId, $accountCode, $orderIncrementId, \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Status\Export $orderStatus, $timeout = 5000)
    {
        $report = $this->getReport();
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('orderIncrementId', $orderIncrementId)
            ->_assertNotEmpty('orderStatus', $orderStatus);

        $transaction = $this->orderStatusTransactionPostFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setOrderIncrementId( $orderIncrementId )
            ->setOrderStatus( $orderStatus->toArray() );

        $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

        return $response;
    }

    function postInstanceProducts($storeId, $accountCode, array $skus, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('skus', $skus);

        $transaction = $this->productTransactionSkusPostFactory->create();
        $transaction
            ->setAccountCode($accountCode)
            ->setSkus($skus);

        $this->submit( $transaction, $this->_getTransport($storeId, $timeout));
    }

    function getProductPricing($storeId, $accountCode, array $pricelists, array $skus = array(), $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('pricelists', $pricelists);

        $transaction = $this->productTransactionPriceGetFactory->create();
        $transaction->setAccountCode($accountCode);
        $transaction->setPricelists($pricelists);

        $report     = $this->getReport();
        $transport  = $this->_getTransport($storeId, $timeout);
        $querylimit = $this->getApiConfig()->getQueryLimit();

        $request = $this->productMessagePriceGetRequestFactory->create();

        $action = $request->buildAction($accountCode, $pricelists, array());
        if (strlen($action) > $querylimit) {
            $report->error('Unable to chunk skus for API request - query limit too low');
            return array();
        }

        // chunking
        $chunks = $rejected = array();

        $chunks = array();
        $accumulator = array();
        foreach ($skus as $_sku) {

            // try only with sku
            $action = $request->buildAction($accountCode, $pricelists, array($_sku));
            if (strlen($action) > $querylimit) {
                // too big to fit, even on its own
                $rejected[] = $_sku;
                continue;
            }

            $accumulator[] = $_sku;
            $action = $request->buildAction($accountCode, $pricelists, $accumulator);

            if (strlen($action) > $querylimit) {
                $last = array_pop($accumulator); // keep the overflow sku

                $chunks[]      = $accumulator; // keep the batch
                $accumulator   = array();      // reset
                $accumulator[] = $last;        // carry over the last sku
            }
        }
        if (!empty($accumulator)) {
            $chunks[] = $accumulator;
        }

        if (!empty($rejected)) {
            $report->debug('Skus rejected by the API ['.implode(",", $rejected).'] - too big to fit in the request.');
        }

        // submit chunks
        $pricelists = array();
        foreach ($chunks as $_skus) {

            $transaction->setSkus($_skus);
            $response = $this->submit($transaction, $transport);

            if ($apiPriceLists = $response->getPricelists()) {
                $pricelists = array_merge($pricelists, $apiPriceLists);
            }
        }

        return $pricelists;
    }

    function getOrderStatus( $storeId, $accountCode, $orderIncrementId, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode)
            ->_assertNotEmpty('orderIncrementId', $orderIncrementId);

        $transaction = $this->orderStatusTransactionGetFactory->create();
        $transaction
            ->setAccountCode( $accountCode )
            ->setExternalId( $orderIncrementId );

        $result = null;

        try {

            $response = $this->submit( $transaction, $this->_getTransport($storeId, $timeout));

            $responseData = ( $status = $response->getResponse() )
                ? $status
                : $response->getError();

            $result = $this->platformDataFactory->create()->map( $responseData );
        }
        catch ( \Hotlink\Framework\Model\Exception\Transport $e ) {
            if ( $e->getCode() != \Hotlink\Brightpearl\Model\Api\Message\Response\AbstractResponse::CODE_NOT_FOUND ) {
                throw $e;
            }
        }

        return $result;
    }
}