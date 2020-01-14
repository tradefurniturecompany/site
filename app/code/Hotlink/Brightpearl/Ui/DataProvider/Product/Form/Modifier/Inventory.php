<?php
namespace Hotlink\Brightpearl\Ui\DataProvider\Product\Form\Modifier;

class Inventory implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const GROUP_INVENTORY_NAME = 'brightpearl_inventory';
    const GROUP_INVENTORY_SCOPE = 'data.product';

    protected $locator;
    protected $brightpearlResourceStockItemCollectionFactory;
    protected $stockRegistryProvider;
    protected $stockConfiguration;

    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Hotlink\Brightpearl\Model\Stock\ItemFactory $stockItemFactory,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    ) {
        $this->locator = $locator;
        $this->stockItemFactory = $stockItemFactory;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->stockConfiguration = $stockConfiguration;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->createInventoryPanel();

        return $this->meta;
    }

    protected function createInventoryPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_INVENTORY_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'         => __( 'Brightpearl Inventory' ),
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'dataScope'     => static::GROUP_INVENTORY_SCOPE,
                                'collapsible'   => true,
                                'sortOrder'     => 10,
                                ],
                            ],
                        ],
                    'children' => $this->getInventoryContainers()
                    ]
                ]
            );


        return $this;
    }

    protected function getInventoryContainers()
    {
        $bpItem = $this->getBrightpearlStockItem();

        $mageIdTxt = !is_null( $bpItem->getItemId() )
            ? $bpItem->getItemId()
            : __('-- no item --');
        $mageId = $this->buildComplexComponent( '<strong>Magento Item ID:</strong> '. $mageIdTxt, 20 );

        $bpLevelTxt = !is_null( $bpItem->getBrightpearlLevel() )
            ? $bpItem->getBrightpearlLevel()
            : __('-- no data --');
        $bpLevel = $this->buildComplexComponent( '<strong>Brightpearl Stock Level:</strong> '. $bpLevelTxt, 30 );

        $timeTxt = !is_null( $bpItem->getTimestamp() )
            ? date( \DateTime::ATOM, $bpItem->getTimestamp() )
            : __( '-- never --' );
        $time = $this->buildComplexComponent( '<strong>Last updated: </strong>' . $timeTxt, 40 );

        return [ 'mage-id'  => $mageId,
                 'bp-level' => $bpLevel,
                 'time'     => $time ];
    }

    protected function getBrightpearlStockItem()
    {
        $product = $this->locator->getProduct();
        $stockItem = $this->stockRegistryProvider->getStockItem(
            $product->getId(),
            $this->stockConfiguration->getDefaultScopeId() );

        if ( !$stockItem->hasBrightpearlStockItem() ) {
            $bpStockItem = $this->stockItemFactory->create()->load( $stockItem->getId(), 'item_id' );
            $stockItem->setBrightpearlStockItem( $bpStockItem );
        }

        return $stockItem->getBrightpearlStockItem();
    }

    protected function buildComplexComponent( $content, $sortOrder )
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => null,
                        'formElement'   => \Magento\Ui\Component\Container::NAME,
                        'componentType' => \Magento\Ui\Component\Container::NAME,
                        'template'      => 'ui/form/components/complex',
                        'sortOrder'     => $sortOrder,
                        'content'       => $content,
                        ],
                    ],
                ]
            ];
    }
}
