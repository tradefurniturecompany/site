<?php
namespace Hotlink\Brightpearl\Helper;

class Stock
{
    protected $mageStockItemRepository;
    protected $mageStockItemSearchCriteria;

    protected $bpStockItemCollectionFactory;
    protected $bpStockItemFactory;

    function __construct(
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $mageStockItemRepository,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterface $mageStockItemSearchCriteria,

        \Hotlink\Brightpearl\Model\ResourceModel\Stock\Item\CollectionFactory $bpStockItemCollectionFactory,
        \Hotlink\Brightpearl\Model\Stock\ItemFactory $bpStockItemFactory )
    {
        $this->bpStockItemCollectionFactory = $bpStockItemCollectionFactory;
        $this->bpStockItemFactory = $bpStockItemFactory;

        $this->mageStockItemRepository = $mageStockItemRepository;
        $this->mageStockItemSearchCriteria = $mageStockItemSearchCriteria;
    }

    function loadBrightpearlInventory( $productsCollection, $createMissingItems = true )
    {
        $prodIds = $productsCollection->walk('getId');

        $this->mageStockItemSearchCriteria->removeFilter( 'product_id' );
        $this->mageStockItemSearchCriteria->addFilter( 'product_id', 'product_id', [ 'in' => $prodIds ], 'public' );
        $stockItemsSearchResult = $this->mageStockItemRepository->getList( $this->mageStockItemSearchCriteria )->getItems();

        $lookup = array();
        if ( $stockItemsSearchResult )
            {
                $bpInventoryCache = $this->bpStockItemCollectionFactory->create()
                                  ->addStockItemFilter( $stockItemsSearchResult ) // todo:
                                  ->load();
                foreach ( $stockItemsSearchResult as $mageStockItem)
                    {
                        $product = $productsCollection->getItemById( $mageStockItem->getProductId() );
                        if ( $bpItem = $bpInventoryCache->getItemByColumnValue( 'item_id', $mageStockItem->getId() ) )
                            {
                                $bpItem->setMagentoStockItem( $mageStockItem );
                                $lookup[ $product->getSku() ] = $bpItem;
                            }
                        else if ( $createMissingItems )
                            {
                                $bpItem = $this->bpStockItemFactory->create();
                                $bpItem->setMagentoStockItem( $mageStockItem );
                                $lookup[ $product->getSku() ] = $bpItem;
                            }
                    }
            }
        return $lookup;
    }

}
