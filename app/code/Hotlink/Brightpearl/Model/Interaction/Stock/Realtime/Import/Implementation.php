<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Stock\Import\AbstractImport
{

    protected $catalogHelper;
    protected $brightpearlWarehouseCollectionFactory;
    protected $brightpearlStockHelper;
    protected $priceIndexer;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $brightpearlApiServiceWorkflowHelper,
        \Hotlink\Framework\Helper\Clock $clockHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,

        \Magento\Catalog\Helper\Data $catalogHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $brightpearlWarehouseCollectionFactory,
        \Hotlink\Brightpearl\Helper\Stock $brightpearlStockHelper,
        \Magento\Catalog\Model\Indexer\Product\Price $priceIndexer
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $transactionFactory,
            $brightpearlApiServiceWorkflowHelper,
            $clockHelper,
            $collectionFactory
        );

        $this->catalogHelper = $catalogHelper;
        $this->brightpearlWarehouseCollectionFactory = $brightpearlWarehouseCollectionFactory;
        $this->brightpearlStockHelper = $brightpearlStockHelper;
        $this->priceIndexer = $priceIndexer;
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl Stock Importer (Real-time)';
    }

    public function execute()
    {
        $report      = $this->getReport();
        $environment = $this->getEnvironment();

        $ttl = $environment->getParameter( 'stock_ttl' )->getValue();
        $productsCollection = $environment->getParameter('stream')->getValue()->getReader()->getCollection();

        $warehouses     = $environment->getParameter('warehouse')->getValue();
        $queryLimit     = $environment->getParameter('query_limit')->getValue();
        $runPriceIndex  = $environment->getParameter('run_price_index')->getValue();
        $putBackInstock = $environment->getParameter('put_back_instock')->getValue();
        $setQtyZeroWhenMissing = $environment->getParameter('set_qty_zero_when_missing')->getValue();
        $skipUnmanaged = $environment->getParameter('skip_unmanaged')->getValue();
        $isPriceGlobal = $this->catalogHelper->isPriceGlobal();
        $sleep = 100;

        $report( $environment, "status" );

        $products = $this->_filterCollection( $productsCollection, '_isProductTypeFlat', $this->_getFlatProductTypeArray() );
        if ( count( $products ) == 0 )
            {
                $report->warn( 'Skipping, no product to process');
                return;
            }

        $skus = $products->walk( 'getSku' );
        $report->addReference( $skus );

        $warehouseColl = $this->brightpearlWarehouseCollectionFactory->create()
            ->addIdFilter( $warehouses )
            ->addActiveFilter();

        if ( count( $warehouseColl ) == 0 )
            {
                $report->error( 'No valid warehouse provided' );
                return;
            }

        $bpInventory = $this->brightpearlStockHelper->loadBrightpearlInventory( $products, true );
        if ( ! ( $expired = $this->_filterExpiredBrightpearlInventory( $bpInventory, $ttl ) ) )
            {
                $report->warn( 'Skipping, no stale products to update' );
                return;
            }

        $apiAvailability = $this->_apiFetchBPAvailability(
            array_keys( $expired ),
            $warehouseColl->walk( 'getBrightpearlId' ),
            $queryLimit
        );

        if ( !$apiAvailability || count( $apiAvailability->getData() ) == 0 )
            {
                $report->warn("No stock availability returned");
                return;
            }

        $reindexIds = $this->update( $products, $bpInventory, $apiAvailability,
                                     $setQtyZeroWhenMissing, $putBackInstock, $skipUnmanaged,
                                     $sleep );
        if ( $isPriceGlobal )
            {
                $report->debug( "price index not executed because price scope is GLOBAL." );
            }
        else if ( $runPriceIndex && $reindexIds )
            {
                // When price is Website scoped, Magento joins the price index table when preparing Bundle Options
                // to determine which options should display and which should not. Basically the presence of a record
                // in the price table influences the bundle options on frontend.
                // When a product goes OUT OF STOCK the price indexer removes the corresponding price record
                // from the price index table, and it creates a record for when a product goes IN STOCK.

                $report->info("running price index for the following product ids: [".implode(',', $reindexIds). "]");

                //
                // we have 2 options:
                // 1. use the index processor: Magento\Catalog\Model\Indexer\Product\Price\Processor::reindexList
                //                             which  based on mode [UPDATE_ON_SAVE or SCHEDULED] reindexes or not.
                // 2. use the indexer direactly: \Magento\Catalog\Model\Indexer\Product\Price::execute
                //
                // we've opted for option nr. 2
                $this->priceIndexer->execute( $reindexIds );
            }
    }

}
