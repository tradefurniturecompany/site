<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    protected $storeManager;
    protected $catalogProductFactory;
    protected $resourceModelProductCollectionFactory;
    
    protected $stockItemCriteriaFactory;
    protected $stockItemRepository;
    protected $stockRegistryStorage;
    protected $stockConfiguration;

    protected $catalogHelper;
    protected $taxConfig;

    protected $apiServiceWorkflowHelper;
    protected $clockHelper;
    protected $sourcePriceLists;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $resourceModelProductCollectionFactory,

        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $stockItemCriteriaFactory,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,

        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Tax\Model\Config $taxConfig,

        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $apiServiceWorkflowHelper,
        \Hotlink\Framework\Helper\Clock $clockHelper,
        \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists $sourcePriceLists
    )
    {
        $this->storeManager = $storeManager;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->resourceModelProductCollectionFactory = $resourceModelProductCollectionFactory;

        $this->stockItemCriteriaFactory = $stockItemCriteriaFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockConfiguration = $stockConfiguration;
        
        $this->catalogHelper = $catalogHelper;
        $this->taxConfig = $taxConfig;

        $this->apiServiceWorkflowHelper = $apiServiceWorkflowHelper;
        $this->clockHelper = $clockHelper;
        $this->sourcePriceLists = $sourcePriceLists;

        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper );
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl Prices Import';
    }

    //
    //  This interaction is usually invoked from the admin store (via admin UI, or cron), therefore the environment
    //  storeid is usually zero. In order for cron to execute the interaction and trigger must be enabled for Admin.
    //  Since Admin is therefore always enabled at runtime, Admin always resides in the list of websites to process.
    //  Running price import at Admin level is therefore obligatory.
    //
    //  When "General/Catalog/Catlog/Price/Catalog Price Scope" is global, the implementation updates the global
    //  price in the Admin store, but also still updates prices each website, however the data is not used by
    //  Magento.
    //
    //  When "General/Catalog/Catlog/Price/Catalog Price Scope" is website, the implementation behaves the same, but
    //  now Magento uses the extra data.
    //

    /*

      NB: Pricing import is hard
          (due to bugs and significant changes still occurring between versions within Magento Core).

      Magento core
          contains bug(s) causing some behaviours to work/fail in particular versions
          unfortunately the workaround for tier price (to fix 2.2.2) causes failure in 2.1.0, due to a different
          bug in 2.1.0 (which has since been resolved in 2.2.2)
      Magento catalog
          api has also been changed, so cannot be used consistently across versions
      Magento repository
          is generalised but makes some scope assumptions, and is dependent on the same code faults
          already discovered (only now they are delegated and even harder to detect), so does not
          offer reliability - only significant performace penalties

      The only viable solutions here were:

         1. Create a new function as required for each version of Magento release (to deal with its bugs)

            (it's not possible to reliably detect the Magento version, since the version detection mechanism
             itself was changed across early versions)

         2. Enforce the required Magento\Catalogue module verion, at the expense of backwards compatibility

      The module enforces option 2.

    */
    public function execute()
    {
        $environment = $this->getEnvironment();
        $report = $this->getReport();
        $report( $environment, "status" );

        $skipAttributes = $environment->getParameter( 'skip_attributes' )->getValue();
        $skipTier       = $environment->getParameter( 'skip_tier' )->getValue();

        if ( $skipAttributes && $skipTier )
            {
                $report->warn( "No activities selected" );
            }
        else
            {
                $scopes = $environment->getParameter( 'websites' )->getValue();
                $skuFilter = $environment->getParameter( 'skus' )->getValue();
                if ( count( $scopes ) )
                    {
                        foreach ( $scopes as $scopeCode => $websites )
                            {
                                foreach ( $websites as $websiteCode => $websiteId )
                                    {
                                        $this->process( $websiteId, $skipAttributes, $skipTier, $skuFilter );
                                    }
                            }
                    }
            }
    }

    public function process( $websiteId, $skipAttributes, $skipTier, $skuFilter )
    {
        $report = $this->getReport();
        $idle = 0;
        $website         = $this->storeManager->getWebsite( $websiteId );
        $websiteName     = $website->getName();
        $defaultStore    = $website->getDefaultStore();
        $storeId         = ( $defaultStore ? $defaultStore->getId()   : null );
        $storeName       = ( $defaultStore ? $defaultStore->getName() : null );

        //  -------------------------------
        //
        //    Check ok to run import
        //
        //  -------------------------------
        if ( is_null( $storeId ) )
            {
                $report->warn( "Skipping, no default store found for website $websiteName [$websiteId]" );
                return;
            }

        if ( ! $this->getInteraction()->getConfig()->isEnabled( $storeId ) )
            {
                $report->warn( "Skipping, interaction disabled for website $websiteName [$websiteId]" );
                return;
            }

        // new env specific for current website. correct scoping is crucial for this interaction.
        $environment = $this->getOrCreateEnvironment( $storeId );
        if ( !$environment->isEnabled() )
            {
                $report->warn( "Skipping, interaction disabled for website $websiteName [$websiteId]" );
                return;
            }
        $trigger = $this->getInteraction()->getTrigger();
        if ( !$environment->isTriggerEnabled( $trigger ) )
            {
                $triggerName = $trigger->getName();
                $report->warn( "Skipping, interaction trigger [$triggerName] disabled for website $websiteName [$websiteId]" );
                return;
            }
    
        $storePriceIncludesTax = $this->taxConfig->priceIncludesTax( $storeId );
        if ( $storePriceIncludesTax )
            {
                if (  $environment->getCheckTaxCompatibility() )
                    {
                        $report->error( "Website [$websiteName] configuration of 'Sales/Tax/Calculation Settings/Catalog Prices' is 'Including Tax'. Brightpearl price lists are always Net of taxes, so this would import incorrect product prices. Reconfigure Magento, or to bypass this error change the interaction 'Perform tax configuration check' configuration setting." )->incFail();
                        return false;
                    }
                else
                    {
                        $report->warn( "'Perform tax configuration check' is set to No in website [$websiteName], so the import is continuing even though 'Sales/Tax/Calculation Settings/Catalog Prices' is configured as 'Including Tax'." );
                    }
            }
        $report->info( "Processing for website [$websiteName] [$websiteId] and store [$storeName] [$storeId]" );

        //  -------------------------------
        //
        //    Determine prices to import
        //
        //  -------------------------------

        $bundleBasePriceList = $environment->getBasePriceList();

        $attributePriceMapping = array();  // default, so as not to fetch unused price lists
        if ( ! $skipAttributes )
            {
                $attributePriceMapping = $environment->getPriceAttributeMapping();
                if ( !$attributePriceMapping )
                    {
                        $report->debug( "No attribute mappings defined for website [$websiteName]" );
                    }
            }

        $tierPriceMapping = array();  // default, so as not to fetch unused price lists
        if ( ! $skipTier )
            {
                $tierPriceMapping = $environment->getTierPriceListMap( $websiteId );
                if ( !$tierPriceMapping )
                    {
                        $report->debug( "No tier price mappings defined for website [$websiteName]" );
                    }
            }

        $importAttrPrices = !$skipAttributes && $attributePriceMapping;
        $importTierPrices = !$skipTier       && $tierPriceMapping;

        if ( !$importAttrPrices && !$importTierPrices )
            {
                $report->debug( 'Skipping, there is nothing to do.' );
                return;
            }

        $importPriceLists = $this->collectPriceLists( $attributePriceMapping,
                                                      $tierPriceMapping,
                                                      $bundleBasePriceList,
                                                      $website );
        if ( 0 == count( $importPriceLists ) )
            {
                $report->error( "No price lists identified" )->incFail();
                return false;
            }

        //  -------------------------------
        //
        //    Prepare product collection
        //
        //  -------------------------------

        //
        //  Magento222 bug: when a collection is iterated, the scope-checker loads attributes using the first
        //                  product encountered. In test cases this produce excludes 'cost'.
        //                  When the next product is processed, Magento fails to identify the 'cost' attribute
        //                  as valid, and initiates a delete.
        //

        // Unset all cached attributes before starting.
        // This goes hand in hand with partial saving and
        // 'Attribute mapping' having (potentially) different values per website.
        $resource = $this->catalogProductFactory->create()->getResource();
        $resource->unsetAttributes( null );
        $resource->isPartialLoad( true );  // No need to load full product after the save

        $collection = $this->resourceModelProductCollectionFactory->create()
                    ->setStoreId( $storeId )        // causes attributes to load from the correct store
                    ->addAttributeToSort( 'sku' )

                    // ISSUE:
                    // Even though the module [Hotlink Brightpearl] only loads the necessary attributes
                    // and also marks the save partial, there is a Magento Framework observer
                    // \Magento\Framework\EntityManager\Observer\AfterEntitySave which loads all attributes (!!!),
                    // which causes the next observer
                    // \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteSavingObserver::execute
                    // to identify that 'visibility' has changed because original_data[visibility] != data[visibility].
                    // Because 'visibility' has changed, it [the second observer] triggers a url_rewrite re-calculation
                    // which creates a duplicate url-key which throws an exception "URL key for specified store already exists.",
                    // causing the save to halt.

                    // FIX:
                    // Load visibility attribute which makes it available in 'original_data' and therefore
                    // the second observer \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteSavingObserver::execute
                    // does not see it as changed.

                    // IMPORTANT:
                    // When this issues comes back, open the second observer,
                    // \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteSavingObserver::execute and check
                    // if Magento has changed the if condition in 'execute' function. If the code detects a
                    // different attribute add it to select.

                    ->addAttributeToSelect( 'visibility' );

        if ( $prodTypes = $environment->getProductTypes() )
            {
                $report->debug( "Filtering products by type: [". implode( ",", $prodTypes ) ."]" );
                $collection->addFieldToFilter( 'type_id', [ 'in' => $prodTypes ] );
            }
        else
            {
                $report->warn( "Skipping, No product type(s) selected" );
                return;
            }

        if ( $websiteId != $this->getAdminWebsiteId() )
            {
                $collection->addWebsiteFilter( $websiteId );
            }

        if ( $skuFilter )
            {
                if ( strlen( $skuFilter ) > 0 )
                    {
                        $collection->addFieldToFilter( "sku", array( 'in' => $skuFilter ) );
                    }
            }

        //
        // Force inclusion of url_key
        //
        // \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteSavingObserver
        //
        //    is incompatible with
        // 
        // \Magento\Eav\Model\ResourceModel\UpdateHandler
        //
        // The latter ignores false values and instead deletes attributes (causing data loss)
        // Magento reloads every attribute each time, and force reloads stock each time, so may as well load all attributes
        // 
        $collection->addAttributeToSelect( '*' );
        if ( $importAttrPrices )
            {
                foreach ( $attributePriceMapping as $map )
                    {
                        $code = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::ATTRIBUTE_CODE ];
                        $collection->addAttributeToSelect( $code ); // only load selected attributes
                    }
                $collection->addAttributeToSelect( 'price_type' ); // needed for bundle products
            }
        $batch = $environment->getBatch();
        $collection->setPageSize( $batch );
        $pages = ( $collection->getSize() > 0 ) ? $collection->getLastPageNumber() : 0;
        if ( $pages == 0 )
            {
                $report->debug( "No products to process" );
                return;
            }

        //  -------------------------------
        //
        //    Start importing
        //
        //  -------------------------------
        $currentPage = 0;
        while ( ++$currentPage <= $pages )
            {
                $report
                    ->info( "Processing batch $currentPage of $pages" )
                    ->setBatch( $currentPage )
                    ->indent();
                $collection
                    ->clear()
                    ->setCurPage( $currentPage )
                    ->addWebsiteNamesToResult()
                    ->load();

                $collection->addOptionsToResult();   // #256 : Needed following changes in Magento 2.3+
                $this->loadStockItemsIntoRegistry( $websiteId, $collection );

                /*
                  Magento210 has bugs forbidding this behaviour:
                       Notice: Undefined variable: websiteId in 
                           ../app/code/Magento/Catalog/Model/ResourceModel/Product/Collection.php on line 2114
                */
                if ( $importTierPrices )
                    {
                        foreach ( $collection as $product )
                            {
                                // This stops the loader applying website exchange rates to tier prices
                                $product->setData( '_edit_mode', true );
                            }
                        $collection->addTierPriceData();
                    }

                $skus = $collection->walk( 'getSku' );
                $report->addReference( $skus );

                if ( $apiPriceLists = $this->_apiFetchPriceLists( $environment, $importPriceLists, $skus ) )
                    {
                        if ( $this->_checkPriceListHeader( $apiPriceLists, $website ) )
                            {
                                foreach ( $collection as $product )
                                    {
                                        $report->info( 'Processing sku ['. $product->getSku() . ']' )->indent();
                                        $product->setStoreId( $storeId );
                                        $attrPricesChanged = false;
                                        $tierPricesChanged = false;
                                        //
                                        //  Attribute price update
                                        //
                                        if ( $importAttrPrices )
                                            {
                                                $attrPricesChanged = $this->applyAttributePriceUpdates(
                                                    $website,
                                                    $product,
                                                    $attributePriceMapping,
                                                    $apiPriceLists,
                                                    $bundleBasePriceList );
                                                $product->setData( \Hotlink\Brightpearl\Workaround\Magento222\Magento\Catalog\Model\Product\Plugin::KEY, $attrPricesChanged );
                                            }
                                        //
                                        //  Tier price update
                                        //
                                        if ( $importTierPrices )
                                            {
                                                $tierPricesChanged = $this->applyTierPriceUpdates(
                                                    $website,
                                                    $product,
                                                    $tierPriceMapping,
                                                    $apiPriceLists,
                                                    $bundleBasePriceList );
                                            }
                                        // $product->setData( 'is_massupdate', true ); // obsolete due to indexer switch
                                        // only save product if necessary
                                        if ( $attrPricesChanged || $tierPricesChanged )
                                            {
                                                $idle += $this->_saveProduct($product);
                                            }
                                        else
                                            {
                                                $report->debug( 'Product save not needed.' );
                                            }
                                        $report->unindent();
                                    }
                            }
                        else
                            {
                                $report->debug( 'Processing halted due to incompatibilities between pricelist(s) and website' )->unindent();
                                break;
                            }
                    }
                else
                    {
                        $report->debug( 'No prices returned from Brightpearl API' );
                    }
                $report->unindent();
            }
        if ( $idle > 0 )
            {
                $report->trace( 'Idle for ' . $idle . ' seconds.' );
            }
    }

    //
    //  Ref \Magento\CatalogInventory\Model\StockRegistryProvider
    //
    protected function loadStockItemsIntoRegistry( $websiteId, $products )
    {
        // see \Magento\CatalogInventory\Model\StockRegistry::getStockItem  ( the passed websiteId is ignored )
        $websiteId = $this->stockConfiguration->getDefaultScopeId();
        $ids = $products->walk( "getId" );
        $criteria = $this->stockItemCriteriaFactory->create();
        $criteria->setProductsFilter( $ids );
        $collection = $this->stockItemRepository->getList( $criteria );
        $counter = 0;
        foreach ( $collection->getItems() as $stockItem )
            {
                if ( $stockItem->getItemId() )
                    {
                        $counter++;
                        $productId = $stockItem->getProductId();
                        $this->getStockRegistryStorage()->setStockItem( $productId, $websiteId, $stockItem );
                    }
            }
        $this->getReport()
            ->info( "Populated catalog inventory registry with $counter stock items" );
    }

    private function getStockRegistryStorage()
    {
        if ( is_null( $this->stockRegistryStorage ) )
            {
                $this->stockRegistryStorage = \Magento\Framework\App\ObjectManager::getInstance()
                                            ->get( \Magento\CatalogInventory\Model\StockRegistryStorage::class );
            }
        return $this->stockRegistryStorage;
    }

    protected function collectPriceLists( array $attrMap,
                                          array $tierMap,
                                          $bundleBasePriceList,
                                          \Magento\Store\Model\Website $website )
    {
        $websiteId = $website->getId();
        $priceLists = array();

        foreach ( $attrMap as $map )
            {
                $priceLists[] = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST ];
            }

        // Do not include "All Websites" scoped tier prices in specific website tier price updates.
        // These should be updated and managed independently.
        foreach ( $tierMap as $map )
            {
                $tierWebsiteId  = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::WEBSITE ];
                if ( ( $tierWebsiteId == $websiteId ) )
                    {
                        $priceLists[] = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST ];
                    }
            }
        $priceLists[] = $bundleBasePriceList;
        return array_unique( $priceLists );
    }

    protected function applyAttributePriceUpdates(\Magento\Store\Model\Website $website,
                                                  \Magento\Catalog\Model\Product $product,
                                                  array $configMap,
                                                  array $apiPriceLists,
                                                  $basePriceList)
    {
        $report    = $this->getReport();
        $isBundle  = ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        $sku       = $product->getSku();
        $resource  = $product->getResource();
        // $pWebsites = $product->getWebsites();  // M1
        $pWebsites = $product->getWebsiteIds(); // M2
        $updates   = array();

        $report->info("Applying attribute price updates")->indent();

        $wid = $website->getId();
        if ($wid != $this->getAdminWebsiteId() && !in_array($wid, $pWebsites)) {
            // do not process product for website if product is not assigned to website, but allow admin website
            $report->debug("not assigned to website [$wid]");
            return;
        }

        $noPriceInList = array();
        foreach ($configMap as $map) {
            $attrCode   = $map[\Hotlink\Brightpearl\Model\Config\Field\Price::ATTRIBUTE_CODE];
            $attr       = $resource->getAttribute($attrCode);
            $listId     = $map[\Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST];
            $behaviour  = $map[\Hotlink\Brightpearl\Model\Config\Field\Price::BEHAVIOUR];
            $currValue  = $product->getData($attrCode);
            $clearValue = $behaviour == \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Missing\Behaviour::BEHAVIOUR_CLEAR;
            $price = $this->_getSkuSinglePrice($apiPriceLists, $listId, $sku);
            if ( !is_null( $price ) )
                {
                    if ( $isBundle )
                        {
                            // only update price if bundle price type is fixed
                            if ($attrCode === 'price' && $product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED)
                                {
                                    $updates[] = array('code' => $attrCode, 'value' => $price);
                                }
                            if ($attrCode === 'special_price')
                                {
                                    $percent = $this->getPercent( $price, $apiPriceLists, $basePriceList, $sku );
                                    if ( $percent )
                                        {
                                            $updates[] = array( 'code' => $attrCode, 'value' => $percent );
                                        }
                                }
                        }
                    else
                        {
                            $updates[] = array( 'code' => $attrCode, 'value' => $price);
                        }
                }
            else {
                $noPriceInList[] = $listId;

                if ($clearValue && $currValue) { // clear value but only if there is a current value
                    $updates[] = array( 'code' => $attrCode, 'value' => '[cleared]');
                    if ($attr->getIsRequired()) {
                        $report->warn("cleared value of required attribute [$attrCode]");
                    }
                }
            }
        }

        $this->reportIfMissingPrices( $noPriceInList );

        if (!empty($updates)) {

            $message = '';
            foreach ($updates as $changed) {
                $attr = $changed['code'];
                $val  = $changed['value'];

                $message .= $attr . '=' . $val .'; ';

                if ($val === '[cleared]') {
                    $val = null;
                }

                $product->setData($attr, $val);
                $resource->getAttribute($attr);
            }

            $report->debug("mapped $message");
        }
        else {
            $report->debug("no attributes mapped");
        }

        $report->unindent();
        return $updates;
    }

    protected function applyTierPriceUpdates( \Magento\Store\Model\Website $website,
                                              \Magento\Catalog\Model\Product $product,
                                              array $tierMap,
                                              array $apiPriceLists,
                                              $basePriceList )
    {
        $sku         = $product->getSku();
        $isBundle    = ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
        $report      = $this->getReport();
        $priceGlobal = $this->catalogHelper->isPriceGlobal();
        $resource    = $product->getResource();
        $wid         = $website->getId();

        $report->info( "Applying tier price updates" )->indent();

        $allTierPrices  = array();
        $websiteTierPrices = array();
        $noPriceInList = array();
        foreach ( $tierMap as $map )
            {
                $websiteId = ( $priceGlobal
                               ? $this->getAdminWebsiteId()
                               : $map[\Hotlink\Brightpearl\Model\Config\Field\Price::WEBSITE ] );
                $custGroup = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::CUSTOMER_GROUP ];
                $listId    = $map[ \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_LIST     ];
                $break     = $map[  \Hotlink\Brightpearl\Model\Config\Field\Price::PRICE_BREAK   ];
                $excBrk1   = ( $break == \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Tier\BreakTier::EXCLUDE_BREAK_1 );
                
                $prices = $this->_getSkuMultiPrice( $apiPriceLists, $listId, $sku );
                if ( !is_null( $prices ) )
                    {
                        foreach ( $prices as $qty => $price )
                            {
                                if ( $qty === 1 && $excBrk1 ) continue;

                                $tierPrice = null;
                                if ( $isBundle )
                                    {
                                        if ( $discountPercent = $this->getDiscountPercent( $price,
                                                                                           $apiPriceLists,
                                                                                           $basePriceList,
                                                                                           $sku  ) )
                                            {
                                                $tierPrice = array( 'price'       => $discountPercent,
                                                                    'website_id'  => $websiteId,
                                                                    'cust_group'  => $custGroup,
                                                                    'price_qty'   => $qty,
                                                                    'delete'      => '' );
                                            }
                                    }
                                else
                                    {
                                        $tierPrice = array( 'price'       => $price,
                                                            'website_id'  => $websiteId,
                                                            'cust_group'  => $custGroup,
                                                            'price_qty'   => $qty,
                                                            'delete'      => '' );
                                    }
                                if ( !is_null( $tierPrice ) )
                                    {
                                        $allTierPrices[] = $tierPrice;
                                        if ( $websiteId == $wid || $priceGlobal )
                                            {
                                                $websiteTierPrices[] = $tierPrice;
                                            }
                                    }
                            }
                    }
                else
                    {
                        $noPriceInList[] = $listId;
                    }
            }

        $this->reportIfMissingPrices( $noPriceInList );

        // Without pre-loaded tier prices, load them nanually here (much much slower)
        // Only works with Magento 2.1.0
        // $oldTierPrices = $resource->getAttribute( 'tier_price' )->getBackend()->afterLoad( $product );

        //
        // Do not use \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface - too slow
        //
        // Only works with Magento 2.2.2
        // $oldTierPrices = $product->getData( 'tier_price' );    // do not getTierPrice() as it invokes filtering

        // Only works with Magento 2.3.*
        $oldTierPrices = $product->getData( 'tier_price' );    // do not getTierPrice() as it invokes filtering
        if ( is_null( $oldTierPrices ) ) $oldTierPrices = [];

        $newTierPrices = $websiteTierPrices;

        $result = $this->mergeTierPricing( $oldTierPrices, $newTierPrices );
        $pricing = $result[ 'pricing' ];
        $inserts = $result[ 'inserts' ];
        $deletes = $result[ 'deletes' ];
        $updates = $result[ 'updates' ];

        $changes = ( count( $inserts ) > 0 ) || ( count( $deletes ) > 0 ) || ( count( $updates ) > 0 );

        if ( $changes )
            {
                $valid = true;
                $product->setTierPrice( $pricing );
                try
                    {
                        $resource->getAttribute( 'tier_price' )->getBackend()->validate( $product );
                    }
                catch ( \Exception $e )
                    {
                        $report->error( 'tier price validation failed - ' . $e->getMessage() );
                        $product->getResource()->unsetAttributes( 'tier_price' );
                        // do not change tier prices when validation fails

                        // Magento <= 2.2 
                        // $product->setTierPrice( false ); 
                        // Magento >= 2.3
                        $this->disableTierPricing( $product );
                        $valid = false;
                        $changes = false;
                    }
                if ( $valid )
                    {
                        if ( empty( $newTierPrices ) )
                            {
                                $report->debug( 'cleared tier prices' );
                            }
                        else
                            {
                                $report->debug( 'mapped tier prices - ' . json_encode( $newTierPrices ) );
                            }
                    }
            }

        if ( ! $changes )
            {
                // do not change tier prices when no changes
                // before Magento 2.3 : must be false (unknown if null is accepted?)
                // after  Magento 2.3 : false is not permitted, must be null
                // $product->setTierPrice( false );
                $this->disableTierPricing( $product );
                $report->debug( 'no tier prices updated' );
            }

        $report->unindent();
        return $changes;
    }

    protected function disableTierPricing( $product )
    {
        $product->setTierPrice( null );
    }

    public function mergeTierPricing( $oldTierPrices, $newTierPrices )
    {
        $inserts = [];
        $deletes = [];
        $updates = [];
        $old = $this->getIndexedTierPrices( $oldTierPrices );
        $new = $this->getIndexedTierPrices( $newTierPrices );
        $result = [];
        foreach ( $new as $key => $item )
            {
                $apply = $item;
                if ( array_key_exists( $key, $old ) )
                    {
                        $apply = $old[ $key ];
                        foreach ( $item as $k => $v )
                            {
                                $apply[ $k ] = $v;
                            }
                        if ( $item[ 'price' ] != $apply[ 'price' ] )
                            {
                                $updates[] = $apply;
                            }
                    }
                else
                    {
                        $inserts[] = $item;
                    }
                $result[] = $apply;
            }
        foreach ( $old as $key => $item )
            {
                if ( ! array_key_exists( $key, $new ) )
                    {
                        $deletes[] = $item;
                    }
            }
        return [ 'pricing' => $result,
                 'inserts' => $inserts,
                 'deletes' => $deletes,
                 'updates' => $updates ];
    }

    public function getIndexedTierPrices( $tierPrices )
    {
        $result = [];
        foreach ( $tierPrices as $tierPrice )
            {
                $key = $this->getTierPriceKey( $tierPrice );
                if ( array_key_exists( $key, $result ) )
                    {
                        throw new \Exception( "Duplicate tier price key $key" );
                    }
                $result[ $key ] = $tierPrice;
            }
        return $result;
    }

    public function getTierPriceKey( $tierPrice )
    {
        $result = [];
        $result[] = array_key_exists( 'website_id', $tierPrice ) ? $tierPrice[ 'website_id' ] : '?';
        $result[] = array_key_exists( 'cust_group', $tierPrice ) ? $tierPrice[ 'cust_group' ] : '?';
        // +0 removes trailing zeros from the value (eg. converts "1.0000" to "1", and "1.20" to "1.2")
        $result[] = array_key_exists( 'price_qty',  $tierPrice ) ? ( $tierPrice[ 'price_qty' ] + 0 ) : '?';
        $result = implode( '-', $result );
        return $result;
    }

    protected function _apiFetchPriceLists( \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $env,
                                            array $priceLists,
                                            array $skus )
    {
        $report    = $this->getReport();
        $api       = $this->apiServiceWorkflowHelper;
        $exception = null;
        $apiLists  = array();

        try
            {
                $apiLists = $report( $api, 'getProductPricing', $env->getStoreId(), $env->getAccountCode(), $priceLists, $skus );
            }
        catch ( \Hotlink\Framework\Model\Exception\Base $e )
            {
                $exception = $e;
            }
        catch ( \Hotlink\Brightpearl\Model\Exception\AbstractException $e )
            {
                $exception = $e;
            }

        if ( $exception )
            {
                $report->error( "Unable to fetch product pricing from Brightpearl", $exception );
                return null;
            }

        $fId = \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::ID;
        $fPrices = \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::PRICES;

        // aggregate lists
        $resultLists = array();
        foreach ( $apiLists as $list )
            {
                $listId = $list[ $fId ];
                if ( !isset( $resultLists[ $listId ] ) )
                    {
                        $resultLists[ $listId ] = array();
                        foreach ( $list as $key => $val )
                            {
                                if ( $key != $fPrices )
                                    {
                                        $resultLists[ $listId ][ $key ] = $val;
                                    }
                                else
                                    {
                                        $resultLists[ $listId ][ $key ] = array();
                                    }
                            }
                    }
                $listPrices = $list[ $fPrices ];
                foreach ( $listPrices as $sku => $pricing )
                    {
                        $resultLists[ $listId ][ $fPrices ][ "$sku" ] = $pricing;
                    }
            }
        return $resultLists;
    }

    protected function getOrCreateEnvironment($storeId)
    {
        return $this->hasEnvironment($storeId)
            ? $this->getEnvironment($storeId)
            : $this->createEnvironment($storeId);
    }

    protected function _checkPriceListHeader( array $apiPriceLists, \Magento\Store\Model\Website $website )
    {
        $report = $this->getReport();
        $valid = true;

        $websiteCurrency = $website->getBaseCurrencyCode();
        $websiteName     = $website->getName();

        foreach ( $apiPriceLists as $list )
            {
                $apiListCode     = $list[ \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::CODE ];
                $apiListCurrency = $list[ \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::CURRENCY_CODE ];
                if ( $apiListCurrency != $websiteCurrency )
                    {
                        $report->error( "Website [$websiteName][$websiteCurrency] and Price list [$apiListCode][$apiListCurrency] have incompatible currencies" )->incFail();
                        $valid = false;
                    }
            }
        return $valid;
    }

    protected function getDiscountPercent($price, array $apiPriceLists, $basePriceList, $sku)
    {
        $report          = $this->getReport();
        $discountPercent = null;

        $basePrice = $this->_getSkuSinglePrice($apiPriceLists, $basePriceList, $sku);
        if ( !is_null( $basePrice ) )
            {
                if ( (float) $basePrice === 0.0 )
                    {
                        $report->warn("[$sku] could not calculate discount percent - division by 0 (zero)");
                    }
                else if ( (float) $basePrice < $price )
                    {
                        $report->warn("[$sku] could not calculate discount percent - base price < price");
                    }
                else
                    {
                        $discountPercent = (1 - ($price / $basePrice)) * 100;
                    }
            }
        else
            {
                $report->warn("[$sku] could not calculate discount percent - base price missing");
            }
        return $discountPercent;
    }

    protected function getPercent($price, array $apiPriceLists, $basePriceList, $sku)
    {
        $report  = $this->getReport();
        $percent = null;

        $basePrice = $this->_getSkuSinglePrice($apiPriceLists, $basePriceList, $sku);
        if ( ! is_null( $basePrice ) )
            {
                if ( (float) $basePrice === 0.0 )
                    {
                        $report->warn( "[$sku] could not calculate percent - division by 0 (zero)" );
                    }
                else if ( $basePrice < $price )
                    {
                        $report->warn( "[$sku] could not calculate percent - base price < price" );
                    }
                else
                    {
                        $percent = 100 * $price / $basePrice;
                    }
            }
        else
            {
                $report->warn( "[$sku] could not calculate percent - base price missing" );
            }
        return $percent;
    }

    protected function _getSkuSinglePrice(array $lists, $listId, $sku)
    {
        if ($prices = $this->getSkuListPrices($lists, $listId, $sku)) {

            if (isset($prices[1])) {
                return $prices[1];
            }
        }

        return null;
    }

    protected function _getSkuMultiPrice(array $lists, $listId, $sku)
    {
        if ($prices = $this->getSkuListPrices($lists, $listId, $sku)) {
            return $prices;
        }

        return null;
    }

    protected function getSkuListPrices(array $lists, $listId, $sku)
    {
        if (isset($lists[$listId][\Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::PRICES][$sku])) {

            return $lists[$listId][\Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Price\Lists::PRICES][$sku];
        }

        return null;
    }

    protected function _saveProduct(\Magento\Catalog\Model\Product $product)
    {
        $sleep  = $this->getEnvironment()->getSleep();
        $report = $this->getReport();
        $sku    = $product->getSku();
        $idle   = 0;

        try
            {
                $product->getResource()->isPartialSave(true);
                //
                //  A. The first part of the save process in Magento2 handles Eav attributes and scopes,
                //  and correctly saves all the designated attributes:
                //
                //     ????
                //
                //  This inserts all new attributes required - for example if the Admin Scope has a 'cost'
                //  attribute, but the the "Main Website" does not (see **) - for each required store (so if
                //  Main Website has 3 stores but no website-scoped value, it creates 3 records, one for each
                //  required store).
                //
                //  B. However once that is completed (and before the mysql transaction is commited),
                //
                //    \Magento\Framework\EntityManager\Operation\Update::execute($entity, $arguments = [])
                //
                //       fires event
                //
                //    magento_catalog_api_data_productinterface_save_after
                //
                //  which is handled in Magento/Framework/EntityManager/Observer/AfterEntitySave.php by
                //
                //      Magento\Framework\EntityManager\Observer\AfterEntitySave::execute(Observer $observer)
                //
                //  If the passed object is an instance of Magento\Framework\Model\AbstractModel, then (in order)
                //
                //  1. $entity->getResource()->loadAllAttributes($entity);
                //
                //     This immediately causes all sorts of problems - eg url rewrite duplication problems
                //
                //  2. $entity->getResource()->afterSave($entity);
                //
                //     This causes all attribute backend models to be walked, and applies afterSave to these.
                //     In particular (for the 'cost' attribute), this function is called:
                //
                //          \Magento\Catalog\Model\Product\Attribute\Backend\Price::afterSave($object)
                //
                //     This checks where the attribute already exists in the scope being saved, and if it
                //     doesn't (and it will not when being created), then it assigns the attribute value to null,
                //     and deletes the price attribute for each store in which it was correctly inserted by (A).
                //
                //     The premise is that if the attribute does not already exist in the scope being saved, then it
                //     should be deleted. Since our intention is to create the attribute in the current scope, this
                //     makes standard product save unusable for adding prices to different website. Workarounds:
                //
                //       i)   overload and adjust the miguided logic
                //              pros: easy to implement
                //              cons: reduces compatibility going forward when problem fixed
                //
                //       i)   overload Price::afterSave($object) and change the isUseDefault behaviour
                //            so that it does not null the value and delete the just created attribute.
                //              pros: easy to implement
                //              cons: reduces compatibility going forward when problem fixed
                //
                //       ii)  utilise attribute save only functions (not a product save)
                //            Try $product->getResource()->saveAttribute( $product, $code );
                //              pros: faster to execute, more reliable
                //              cons: cannot tell what else is bypassed (eg. index updates)
                //
                //       iii) find a subtle flag to cause Magento to bypass this behaviour
                //              pros: should not require changes if/when the bug is fixed
                //              cons: potentially brittle, or may not exist
                //
                //       iv)  manually add the wanted attributes to
                //              Magento\Catalog\Model\Attribute\ScopeOverriddenValue
                //
                //       v)   mimic admin ui behaviour (ie. pretend to be a Ui even though we aren't)
                //              pros: most compliant approach (at least for current verison of M2)
                //              cons: inefficent and mindless, we are not a ui
                //
                //    Other solution considerations:
                //
                //       i.   saving product adjusts catalogue price rules, which may require all attributes
                //       ii.  saving products rebuilds indexes
                //       iii. saving products clears caches
                //       iv.  saving products invokes events used by thrid party modules
                //
                //    Therefore safest to use product save for maximum compliance.
                //
                //  3. $entity->afterSave();
                //
                //     ????
                //
                //  4. $entity->getResource()->addCommitCallback([$entity, 'afterCommitCallback']);
                //
                //     ????
                //
                //  ** IE. no cost attribute record exists in catalog_product_entity_decimal for Main Website stores,
                //         and when the product is viewed in the admin screens under Main Website, the cost field
                //         value is empty and the "Use Default" checkbox is ticked
                //
                //  The behaviour has been changed to resolve a bug:
                //
                //  create a record when an attribute value is null?
                //    Magento210 : No
                //    Magento222 : Yes
                //
                //  save a product with a price attribute having website scope:
                //
                //    Magento210 : [BUG] create a record only for default website store only
                //                       no derivable Admin record and no record in other stores
                //
                //    Magento222 : [FIX] create a record in each website store
                //

                //   #256 : Magento 2.3+ (new bugs introduced)
                //
                //   Now the custom options flags are explicitly set to false prior to save by the entity updater.
                //       Invoked via event : magento_catalog_api_data_productinterface_save_before
                //       Stack trace:
                //       \Magento\Framework\EntityManager\Observer\BeforeEntitySave::execute(...)
                //            $entity->beforeSave();
                //       \Magento\Catalog\Model\Product::beforeSave()
                //            $this->setHasOptions(false);
                //   Therefore existing custom options are always lost, unless they are re-saved in entirety.
                //   To allow resaving, the "can_save_custom_options" flag must be set, and the custom options
                //   must be loaded for the product (via addOptionsToResult).
                //
                $product->setCanSaveCustomOptions( true );

                $product->save();

                if ($sleep > 0)
                    {
                        $idleStart = $this->clockHelper->microtime_float();
                        usleep($sleep);
                        $idleEnd = $this->clockHelper->microtime_float();
                        $idle = $idleEnd - $idleStart;
                    }
                $report->info("[$sku] successfully saved")->incSuccess();
            }
        catch ( \Exception $e)
            {
                $report->error("[$sku] unable to save - ".$e->getMessage())->incFail();
            }
        return $idle;
    }

    protected function getWebsites(\Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $env)
    {
        $report    = $this->getReport();
        $validWIds = array();

        $param = $env->getParameter('websites')->getValue();
        if (is_null($param) || (is_array($param) && empty($param))) {
            $report->error('Website(s) is required');
        }
        else {
            // collect ids
            $websiteIds = array();

            if (isset($param['admin'])) {
                $websiteIds = array_values($param['admin']);
            }
            if (isset($param['website'])) {
                $websiteIds = array_merge($websiteIds, array_values($param['website']));
            }

            foreach ($websiteIds as $wId) {
                try {

                    $this->storeManager->getWebsite($wId); // Magento throws exception on invalid website id.
                    $validWIds[] = $wId;
                }
                catch ( \Exception $e) {
                    $report->error("Invalid website with id [$wId]");
                }
            }
        }

        return $validWIds;
    }

    protected function getAdminWebsiteId()
    {
        return 0;
    }

    protected function reportIfMissingPrices( $noPriceInList )
    {
        $info = array();
        foreach ( array_unique( $noPriceInList ) as $id )
            {
                $info[] = $id . ":" . $this->sourcePriceLists->getName( $id );
            }
        if ( $info )
            {
                $info = implode( ' , ', $info );
                $this->getReport()->debug( "no price found in list $info" );
            }
    }

}
