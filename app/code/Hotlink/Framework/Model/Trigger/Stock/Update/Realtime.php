<?php
namespace Hotlink\Framework\Model\Trigger\Stock\Update;

class Realtime extends \Hotlink\Framework\Model\Trigger\AbstractTrigger implements \Magento\Framework\Event\ObserverInterface
{

    const KEY_SHOPPER_ON_VIEW_PRODUCT       = 'on_shopper_view_product';
    const KEY_SHOPPER_ON_ADD_TO_BASKET      = 'on_shopper_add_to_basket';
    const KEY_SHOPPER_ON_VIEW_BASKET        = 'on_shopper_view_basket';
    const KEY_SHOPPER_ON_CHECKOUT_PAGE      = 'on_shopper_checkout_page';
    const KEY_SHOPPER_ON_CHECKOUT_ORDER     = 'on_shopper_checkout_order';
    const KEY_ADMIN_ON_VIEW_PRODUCT         = 'on_admin_view_product';
    const KEY_ADMIN_ON_PLACE_ORDER          = 'on_admin_place_order';

    const LABEL_SHOPPER_ON_VIEW_PRODUCT       = 'On shopper view product';
    const LABEL_SHOPPER_ON_ADD_TO_BASKET      = 'On shopper add to basket';
    const LABEL_SHOPPER_ON_VIEW_BASKET        = 'On shopper view basket';
    const LABEL_SHOPPER_ON_CHECKOUT_PAGE      = 'On shopper checkout page';
    const LABEL_SHOPPER_ON_CHECKOUT_ORDER     = 'On shopper checkout order';
    const LABEL_ADMIN_ON_VIEW_PRODUCT         = 'On admin view product';
    const LABEL_ADMIN_ON_PLACE_ORDER          = 'On admin place order';

    protected $request;
    protected $registry;
    protected $bundleOptionFactory;
    protected $collectionFactory;
    protected $stockHelper;
    protected $stockRegistryStorage;
    protected $scopeHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        \Magento\Bundle\Model\OptionFactory $bundleOptionFactory,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\CatalogInventory\Model\StockRegistryStorage $stockRegistryStorage,
        \Hotlink\Framework\Helper\Scope $scopeHelper
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );

        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->registry = $registry;
        $this->bundleOptionFactory = $bundleOptionFactory;
        $this->stockHelper = $stockHelper;
        $this->stockRegistryStorage = $stockRegistryStorage;
        $this->scopeHelper = $scopeHelper;
    }

    protected function _getName()
    {
        return 'Realtime Stock Update';
    }

    public function getMagentoEvents()
    {
        return
            [
                'On product view'          => 'catalog_controller_product_view',
                'On product add to basket' => 'sales_quote_product_add_after',
                'On view basket items'     => 'sales_quote_item_collection_products_after_load',
                'On checkout submit order' => 'checkout_submit_before',
                'On admin add to basket'   => 'catalog_product_edit_action'
            ];
    }

    protected function _execute()
    {
        $interactions = $this->getExecutableInteractions();

        if ( count( $interactions ) > 0 )
            {
                $event      = $this->getMagentoEvent();
                $storeId    = $this->storeManager()->getStore()->getStoreId();
                $context    = $this->getContext();
                $collection = $this->_getCollectionByContext( $context, $event );
                foreach ( $interactions as $interaction )
                    {
                        $interaction->setTrigger( $this );
                        if ( !$interaction->hasEnvironment( $storeId ) )
                            {
                                $interaction->createEnvironment( $storeId );
                            }
                        foreach ( $interaction->getEnvironments() as $environment )
                            {
                                if ( $stream = $environment->getParameter('stream') )
                                    {
                                        $stream->getValue()->open( $collection );
                                    }
                                if ( $scopes = $environment->getParameter('scopes') )
                                    {
                                        $website = $this->storeManager()->getStore()->getWebsite();
                                        $scopes->setValue( [ 'website' =>
                                                             [ $website->getCode() => $website->getWebsiteId() ] ] );
                                    }
                                if ( ( $context == self::KEY_SHOPPER_ON_VIEW_PRODUCT )
                                     && ( $runPriceIndexParam = $environment->getParameter( 'run_price_index' ) )
                                     && ( $event->getProduct()->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE ) )
                                    {
                                        $runPriceIndexParam->setValue( true );
                                    }
                            }
                        $interaction->execute();
                    }
                $this->_refreshStockInfo( $collection );
            }
    }

    public function getContexts()
    {
        return array(
                     self::KEY_SHOPPER_ON_VIEW_PRODUCT       => self::LABEL_SHOPPER_ON_VIEW_PRODUCT,
                     self::KEY_SHOPPER_ON_ADD_TO_BASKET      => self::LABEL_SHOPPER_ON_ADD_TO_BASKET,
                     self::KEY_SHOPPER_ON_VIEW_BASKET        => self::LABEL_SHOPPER_ON_VIEW_BASKET,
                     self::KEY_SHOPPER_ON_CHECKOUT_PAGE      => self::LABEL_SHOPPER_ON_CHECKOUT_PAGE,
                     self::KEY_SHOPPER_ON_CHECKOUT_ORDER     => self::LABEL_SHOPPER_ON_CHECKOUT_ORDER,
                     self::KEY_ADMIN_ON_VIEW_PRODUCT         => self::LABEL_ADMIN_ON_VIEW_PRODUCT,
                     self::KEY_ADMIN_ON_PLACE_ORDER          => self::LABEL_ADMIN_ON_PLACE_ORDER
                     );
    }

    public function getContext()
    {
        $event      = $this->getMagentoEvent();
        $eventName  = $event->getName();
        $uri        = $this->request->getRequestUri();
        $route      = $this->request->getRouteName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $fullAction = $this->request->getFullActionName();

        $context = false;
        switch ( $eventName )
            {
                case 'catalog_controller_product_view':
                    $context = self::KEY_SHOPPER_ON_VIEW_PRODUCT;   // witnessed
                    break;

                case 'sales_quote_product_add_after':
                    $context = self::KEY_SHOPPER_ON_ADD_TO_BASKET;
                    break;

                case 'checkout_submit_before':
                    $context = self::KEY_SHOPPER_ON_CHECKOUT_ORDER;
                    break;

                case 'sales_quote_item_collection_products_after_load':
                    switch (  $fullAction )
                        {
                            case 'sales_order_create_save':
                                if ( $this->scopeHelper->isAdmin() )
                                    {
                                        $context = self::KEY_ADMIN_ON_PLACE_ORDER;
                                    }
                                break;

                            case 'multishipping_checkout_overviewPost':
                                $context = self::KEY_SHOPPER_ON_CHECKOUT_ORDER;
                                break;

                            case 'checkout_index_index':
                            case 'multishipping_checkout_register':
                            case 'multishipping_checkout_index':
                            case 'multishipping_checkout_addresses':
                            case 'multishipping_checkout_addressesPost':
                            case 'multishipping_checkout_shipping':
                            case 'multishipping_checkout_shippingPost':
                            case 'multishipping_checkout_billing':
                            case 'multishipping_checkout_overview':
                                $context = self::KEY_SHOPPER_ON_CHECKOUT_PAGE;
                                break;

                            default:
                                //TODO: check if mini-basket uses same behaviour
                                if ( $this->request->isAjax() && $fullAction == '__' )
                                    {
                                        $context = false;
                                    }
                                else
                                    {
                                        $context = self::KEY_SHOPPER_ON_VIEW_BASKET;
                                    }
                        }
                    break;

                case 'catalog_product_edit_action':
                    $context = self::KEY_ADMIN_ON_VIEW_PRODUCT;
                    break;
            }
        return $context;
    }

    protected function _getCollectionByContext( $context, $event )
    {
        // Did you know ?
        // - when Display Out of Stock Products is No the collection only
        // contains IN STOCK products. So this trigger (real-time) will never be able
        // to update stock for out of stock products.

        $items = [];

        switch ( $context )
            {
                // When shopper views a product page (sales quote event after load is invoked via javascript)
                case self::KEY_SHOPPER_ON_VIEW_PRODUCT:
                    $product = $event->getProduct();
                    $items = $this->_getAssociatedProductArray( $product );
                    $items[] = $product;
                    break;

                case self::KEY_SHOPPER_ON_ADD_TO_BASKET:
                    $items = $event->getItems();   // child items are included in items
                    break;

                case self::KEY_SHOPPER_ON_VIEW_BASKET:
                case self::KEY_SHOPPER_ON_CHECKOUT_PAGE:
                case self::KEY_ADMIN_ON_PLACE_ORDER:
                    $items = $event->getCollection();
                    break;

                case self::KEY_SHOPPER_ON_CHECKOUT_ORDER:
                    if ( $quote = $event->getQuote() )
                        {
                            $items = $quote->getAllItems();    // one-page checkout
                        }
                    else
                        {
                            $items = $event->getCollection();  // multi-shipping checkout
                        }
                    break;

                case self::KEY_ADMIN_ON_VIEW_PRODUCT:
                    $items = [ $event->getProduct() ];
                    break;

                default:
                    break;
            }

        return $this->_createCollection( $items );
    }

    protected function _refreshStockInfo( \Magento\Framework\Data\Collection $collection )
    {
        foreach ( $collection as $product )
            {
                if ( $product->getHotlinkBrightpearlStockUpdated() )
                    {
                        // only refresh if needed
                        $this->_recursiveStockRefresh( $product );
                    }
            }
    }

    protected function _recursiveStockRefresh( \Magento\Catalog\Model\Product $product )
    {
        $productId = $product->getId();
        $this->stockRegistryStorage->removeStockStatus( $productId );
        $this->stockRegistryStorage->removeStockItem( $productId );

        $this->stockHelper->assignStatusToProduct( $product );
        $product->setStockStatus( $product->getData( 'is_salable' ) );   // set status from is_salable flag

        foreach ( $this->_getAssociatedProductArray( $product ) as $child )
            {
                $this->_recursiveStockRefresh($child);
            }
        return $this;
    }

    protected function _getAssociatedProductArray( \Magento\Catalog\Model\Product $product )
    {
        $type = $product->getTypeId();
        $typeInstance = $product->getTypeInstance( true );

        $result = [];

        switch ( $type )
            {
                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    $result = $typeInstance->getAssociatedProducts( $product );
                    break;

                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    $result = $typeInstance->getUsedProducts( $product, null );
                    break;

                case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                    // Bundle module has an Observer (Mage_Bundle_Model_Observer) that adds additional information to each Option.
                    // using $typeInstance->getOptionsIds($product); the Options collection gets cached
                    // and the Observer never gets to add its data.
                    // therefore get a list of options ids using the resource directly.
                    // $options = $typeInstance->getOptionsIds($product);
                    // @see Mage_Bundle_Model_Product_Type::getOptionsCollection
                    $options = $this->bundleOptionFactory->create()->getResourceCollection()
                             ->setProductIdFilter( $product->getId() )
                             ->joinValues( $product->getStoreId() )
                             ->getAllIds();
                    $bundleOptionsCollection = $typeInstance->getSelectionsCollection($options , $product );
                    $bundleOptionsCollection->addAttributeToSelect('sku');
                    $result = $bundleOptionsCollection->getItems();
            }
        return $result;
    }

    /*
    protected function getAssociatedCollection(\Magento\Catalog\Model\Product $product)
    {
        $type = $product->getTypeId();
        $typeInstance = $product->getTypeInstance(true);

        if ($type == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return $this->_addItemsToColl($typeInstance->getAssociatedProducts($product), $this->collectionFactory->create());
        }
        else if ($type == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $products = $typeInstance->getUsedProducts( $product, null );
            return $this->_addItemsToColl($products, $this->collectionFactory->create());
        }
        else if ($type == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            // Bundle module has an Observer (Mage_Bundle_Model_Observer) that adds some additional information on each Option.
            // using $typeInstance->getOptionsIds($product); the Options collection gets cached
            // and the Observer never gets to add its data.
            // therefore get a list of options ids using the resource directly.
            // $options = $typeInstance->getOptionsIds($product);
            // @see Mage_Bundle_Model_Product_Type::getOptionsCollection
            $options = $this->bundleOptionFactory->create()->getResourceCollection()
                ->setProductIdFilter( $product->getId() )
                ->joinValues( $product->getStoreId() )
                ->getAllIds();

            $bundleOptionsCollection = $typeInstance->getSelectionsCollection($options , $product );
            $bundleOptionsCollection->addAttributeToSelect('sku');
            return $this->_addItemsToColl($bundleOptionsCollection->getItems(), $this->collectionFactory->create());
        }

        return null;
    }
    */

    protected function _createCollection( $items )
    {
        $collection = $this->collectionFactory->create();
        foreach ( $items as $item )
            {
                // collection add throws Exception on duplicate ids.
                try
                    {
                        if ( $item instanceof \Magento\Catalog\Model\Product )
                            {
                                $collection->addItem( $item );
                            }
                        else
                            {
                                $collection->addItem( $item->getProduct() );
                            }
                    }
                catch ( \Exception $e )
                    {
                    }
            }
        return $collection;
    }

    protected function _addItemsToColl($items, \Magento\Framework\Data\Collection $coll)
    {
        foreach ( $items as $_item )
            {
                try
                    {
                        // throws Exception on duplicate ids.
                        $coll->addItem($_item);
                    }
                catch ( \Exception $e )
                    {
                    }
            }
        return $coll;
    }

}
