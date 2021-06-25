<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Import;

abstract class AbstractImport extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    protected $brightpearlApiServiceWorkflowHelper;
    protected $clockHelper;
    protected $collectionFactory;
    protected $transactionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $brightpearlApiServiceWorkflowHelper,
        \Hotlink\Framework\Helper\Clock $clockHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory
        )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->brightpearlApiServiceWorkflowHelper = $brightpearlApiServiceWorkflowHelper;
        $this->clockHelper = $clockHelper;
        $this->transactionFactory = $transactionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    protected function _getFlatProductTypeArray()
    {
        return [ \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                 \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                 \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
                 'giftcard'       // TODO: confirm exists in M2 ?!
        ];
    }

    protected function _apiFetchBPAvailability(array $skus, array $wrhouses, $queryLimit = 512)
    {
        $report = $this->getReport();
        $env    = $this->getEnvironment();

        $report->info("Requesting stock availability with query limit $queryLimit");

        return $report( $this->brightpearlApiServiceWorkflowHelper,
                        'getProductAvailability',
                        $env->getStoreId(),
                        $env->getAccountCode(),
                        $skus,
                        $wrhouses,
                        $env->getApiTimeout() );
    }

    protected function _filterValidBrightpearlAvailability(\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Stock\ListStock $stockList)
    {
        $report = $this->getReport();

        $report
            ->info("Validating stock availability")
            ->indent();

        $report( $stockList, 'validate' );

        $report->unindent();

        $validStock = $stockList->filterValid();

        $nrValidStock = count($validStock);
        if ($nrValidStock == 0)
            return null;

        $nrList = count($stockList);
        if ($nrValidStock !== $nrList)
            $report->warn("$nrValidStock/$nrList valid stock");

        return $validStock;
    }

    protected function _filterExpiredBrightpearlInventory($bpInventory, $ttl = 0)
    {
        $report = $this->getReport();

        $report
            ->indent()
            ->info('Filtering stale inventory records (with ttl ' . $ttl . ')');

        $stale = $this->_filter($bpInventory, '_stockExpired', $ttl);

        $nrInventory = count($bpInventory);
        $nrStale = count($stale);
        if ($nrInventory == $nrStale) {
            $report->info("All $nrInventory inventory records stale.");
        }
        else {
            $report->info("$nrStale / $nrInventory inventory records stale.");
        }
        $report->unindent();

        return $stale;
    }

    /* protected function _filterProductTypeFlat(array $streamProducts) */
    /* { */
    /*     $types = $this->_getFlatProductTypeArray(); */
    /*     $this->getReport()->debug('Filtering products by type '. implode(",", $types)); */

    /*     return $this->_filter($streamProducts, '_isProductTypeFlat', $types); */
    /* } */

    protected function _mapBPStockInfoToBPInventory(
        \Hotlink\Brightpearl\Model\Stock\Item $bpItem,
        $sku,
        $stockLevel,
        $putBackInstock = true )
    {
        $report = $this->getReport();

        $mageItem = $bpItem->getMagentoStockItem();

        $bpItem->setBrightpearlLevel( $stockLevel );
        $mageItem->setQty( $stockLevel );

        $minqty = ( ($min = $mageItem->getMinQty() ) !== null) ? $min : 0;

        if ( $putBackInstock and $stockLevel > $minqty ) {
            $mageItem->setIsInStock( \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK );
            $report->debug(' stock status changed to [IN_STOCK]');
        }

        $report->debug('stock qty set to ['.$stockLevel.']');

        return $this;
    }

    /**
     * save magento and BP stock item.
     */
    protected function _saveStockItem($bpItem, $sku, $sleep = 0)
    {
        $report = $this->getReport();
        $idle = 0;
        try {

            if ($mageStockItem = $bpItem->getMagentoStockItem()) {
                $actionMsg = $bpItem->getId() ? 'updated' : 'created';

                $transaction = $this->transactionFactory->create();
                $transaction->addObject( $mageStockItem );
                $transaction->addObject( $bpItem );
                $transaction->save();

                $report->debug( 'stock item updated, BP stock cache item ' . $actionMsg )->incSuccess();

                if ($sleep > 0) {
                    $idleStart = $this->clockHelper->microtime_float();
                    usleep($sleep);
                    $idleEnd = $this->clockHelper->microtime_float();
                    $idle += $idleEnd - $idleStart;
                }
            }
        }
        catch ( \Exception $e ) {
            $report->error( 'failed to save : ' . $e->getMessage() )->incFail();
        }

        return $idle;
    }

    /**
     *  array helper functions
     */

    protected function _index($coll, $attr)
    {
        $result = array();
        foreach ($coll as $item) {
            $result[$item->getData($attr)] = $item;
        }
        return $result;
    }

    protected function _filter(array $items, $filterFn, $extra)
    {
        $result = array();
        foreach ($items as $index => $item) {
            if (call_user_func(array($this, $filterFn), $item, $extra)) {
                if (!is_null($index))
                    $result[$index] = $item;
                else
                    $result[] = $item;
            }
        }
        return $result;
    }

    protected function _filterCollection( $collection, $filterFn, $extra)
    {
        $newItems = $this->_filter( $collection->getItems(),  $filterFn, $extra);
        $newCol   = $this->collectionFactory->create();

        foreach ($newItems as $item) {
            $newCol->addItem( $item );
        }

        return $newCol;
    }

    protected function _extract($coll, $attr)
    {
        return array_keys($this->_index($coll, $attr));
    }

    /**
     * functions used in conjunction with array helper functions
     */

    protected function _stockExpired($item, $ttl)
    {
        $now = time();
        $expires = $now - $ttl;
        $timestamp = $item->getTimestamp();
        return (is_null($timestamp) || ($timestamp < $expires));
    }

    protected function _sumAvailability( $items )
    {
        $total = 0;
        foreach ( $items as $item )
            {
                $data = $item->getData();
                $qty = array_key_exists( "available", $data ) ? (int) $item[ "available" ] : 0;
                $total += $qty;
            }
        return $total;
    }

    protected function update( $products, $inventory, $apiAvailability,
                               $setQtyZeroWhenMissing, $putBackInstock, $skipUnmanaged,
                               $sleep )
    {
        $reindexIds = [];
        $report = $this->getReport();
        $idle = 0;
        foreach ( $products as $product )
            {
                $sku = $product->getSku();
                $report->debug( "processing $sku" )->indent();
                if ( isset( $inventory[ $sku ] ) )
                    {
                        $item = $inventory[ $sku ];
                        if ( isset( $apiAvailability[ $sku ] ) )   // Item returned in response
                            {
                                $report->debug( "$sku returned from api" );
                                $apiItem = $apiAvailability[ $sku ];
                                $aggregatedStockLevel = $this->_sumAvailability( $apiItem, $setQtyZeroWhenMissing );
                                $manageStock = ( int ) $item->getMagentoStockItem()->getManageStock();
                                if ( $manageStock === 0 && $skipUnmanaged )
                                    {
                                        $report->warn( "$sku skip: stock is unmanaged" );
                                    }
                                else
                                    {
                                        $this->_mapBPStockInfoToBPInventory( $item, $sku, $aggregatedStockLevel, $putBackInstock );
                                        $idle += $this->_saveStockItem( $item, $sku, $sleep );
                                        $reindexIds[] = $sku;
                                    }
                                $report->info( "$sku updated to " . $item->getMagentoStockItem()->getQty() );
                            }
                        else   // Item missing from response
                            {
                                $report->warn( "$sku missing from api response" );
                                // sku is not in BP, what to do?
                                if ( $setQtyZeroWhenMissing )
                                    {
                                        $this->_mapBPStockInfoToBPInventory( $item, $sku, 0, $putBackInstock );
                                        $idle += $this->_saveStockItem( $item, $sku );
                                    }
                                else
                                    {
                                        $report->debug( "$sku skipping, not in Brightpearl and Zero qty when missing is disabled" );
                                    }
                            }
                    }
                else
                    {
                        $report->error( "$sku missing BP inventory record (was it deleted?)" )->incFail();
                    }
                if ( $idle > 0 )
                    {
                        $report->trace( 'Idle for ' . $idle . ' seconds.' );
                    }
                $report->unindent();
            }
        return $reindexIds;
    }

    protected function _sumAvailabilityOld( $warehousesLevel, $zeroOnEmptyFlag = true )
    {
        if (count($warehousesLevel) == 0) {
            return $zeroOnEmptyFlag ? 0 : null;
        }

        $totalAvailability = 0;
        foreach ( $warehousesLevel as $item ) {
            $data = $item->getData();

            if ( !array_key_exists("available", $data) && !$zeroOnEmptyFlag ) {
                return null; // do not set qty to 0 if flag is false
            }

            $qty = array_key_exists("available", $data) ? (int) $item["available"] : 0;

            $totalAvailability += $qty;
        }

        return $totalAvailability;
    }

    protected function _isProductTypeFlat(\Magento\Catalog\Model\Product $product, array $flatTypes)
    {
        return in_array($product->getTypeId(), $flatTypes);
    }
}
