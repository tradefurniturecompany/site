<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import;

class Implementation extends \Hotlink\Brightpearl\Model\Interaction\Stock\Import\AbstractImport
{

    protected $warehouseCollectionFactory;
    protected $productCollectionFactory;
    protected $stockHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $brightpearlApiServiceWorkflowHelper,
        \Hotlink\Framework\Helper\Clock $clockHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,

        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory,
        \Hotlink\Brightpearl\Helper\Stock $stockHelper
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

        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockHelper = $stockHelper;
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl Stock Importer (Bulk)';
    }

    function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $loadBatch = (int) $environment->getParameter('batch')->getValue();
        $sleep = (int) $environment->getParameter('sleep')->getValue();
        $warehouses = $environment->getConfig()->getWarehouses();
        $queryLimit = $environment->getParameter('query_limit')->getValue();
        $putBackInstock = $environment->getConfig()->getPutBackInstock();
        $setQtyZeroWhenMissing = $environment->getConfig()->getQtyZeroWhenMissing();
        $skipUnmanaged = $environment->getConfig()->getSkipUnmanaged();

        $report( $environment, "status" );

        $warehouseColl = $this->warehouseCollectionFactory->create()
            ->addIdFilter($warehouses)
            ->addActiveFilter();

        if (count($warehouseColl) == 0) {
            $report->error('No valid warehouse(s) provided');
            return;
        }

        $productTypes = $this->_getFlatProductTypeArray();
        $report->info("Loading Magento products in batches of $loadBatch ".
                      " where product_type is one of: [". implode(",", $productTypes) .']' );

        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect('sku')
            ->addAttributeToFilter( 'type_id', array('in' => $productTypes) )
            ->setPageSize( $loadBatch )
            ->setStoreId( $environment->getStoreId()) ;

        $pages = ( $collection->getSize() > 0 )
            ? $collection->getLastPageNumber()
            : 0;

        if ( $pages ) {

            $currentPage = 0;
            while (++$currentPage <= $pages) {

                $report
                    ->indent()
                    ->info("Processing batch $currentPage of $pages")
                    ->setBatch($currentPage);

                $collection->clear();
                $collection->setCurPage($currentPage);

                $skus  = $collection->walk('getSku');
                $warehouseIds = $warehouseColl->walk('getBrightpearlId');
                $warehouseNames = $warehouseColl->walk('getName');
                $report->addReference($skus);
                $report->addReference($warehouseNames);

                $apiAvailability = $this->_apiFetchBPAvailability($skus, $warehouseIds, $queryLimit);
                if (!$apiAvailability || count( $apiAvailability->getData() ) == 0) {
                    $report->warn("No stock availability returned")->unindent();
                    continue;
                }

                $apiAvailabilityData = $apiAvailability->getData();
                $bpInventoryItems = $this->stockHelper->loadBrightpearlInventory( $collection, true );

                $this->update( $collection, $bpInventoryItems, $apiAvailability,
                               $setQtyZeroWhenMissing, $putBackInstock, $skipUnmanaged,
                               $sleep );

                $report->unindent();
            }
        }
        else {
            $report->debug( "No products found that satisfy these filters." );
        }

        $report->setBatch( false );
        return $this;
    }
}
