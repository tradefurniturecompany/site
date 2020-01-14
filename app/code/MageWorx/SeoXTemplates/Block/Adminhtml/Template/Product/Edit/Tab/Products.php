<?php
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as DataHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetCollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\ScopeInterface;

/**
 * @method Product setUseAjax(\bool $useAjax)
 * @method array|null getTemplateProducts()
 * @method Product setTemplateProducts(array $products)
 */
class Products extends ExtendedGrid implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    protected $setCollectionFactory;

    protected $productFactory;

    /**
     *
     * @param CollectionFactory $productCollectionFactory
     * @param ProductType $type
     * @param ProductStatus $status
     * @param ProductVisibility $visibility
     * @param Registry $registry
     * @param SetCollectionFactory $setsFactory
     * @param ProductFactory $productFactory
     * @param Context $context
     * @param DataHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductType $type,
        ProductStatus $status,
        ProductVisibility $visibility,
        Registry $registry,
        SetCollectionFactory $setsFactory,
        ProductFactory $productFactory,
        Context $context,
        DataHelper $backendHelper,
        array $data = []
    ) {
    
        $this->productCollectionFactory = $productCollectionFactory;
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->registry = $registry;
        $this->setCollectionFactory = $setsFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getProductTemplate()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isXmlHttpRequest() || $this->_request->getParam('isAjax');
    }

    /**
     * Prepare the collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('price');
        $storeId = $this->getProductTemplate()->getStoreId();
        $collection->joinAttribute('product_name', 'catalog_product/name', 'entity_id', null, 'left', $storeId);
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'left', $storeId);
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'left', $storeId);

        if ($this->getProductTemplate()->getId()) {
            $constraint = '{{table}}.template_id='.$this->getProductTemplate()->getId();
        } else {
            $constraint = '{{table}}.template_id=0';
        }

        $collection->joinField(
            '',
            'mageworx_seoxtemplates_template_relation_product',
            '',
            'product_id = entity_id',
            $constraint,
            'left'
        );

        $excludeProductIds = $this->getProductTemplate()->getProductIdsAssignedForAnalogTemplate();
        if (!empty($excludeProductIds)) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeProductIds);
        }

        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'entity_id',
                'type'             => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header'           => __('Name'),
                'index'            => 'product_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header'           => __('Type'),
                'index'            => 'type_id',
                'type'             => 'options',
                'options'          => $this->type->getOptionArray(),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type'
            ]
        );
        /** @var \Magento\Catalog\Model\ResourceModel\Product $resource */
        $resource = $this->productFactory->create()->getResource();
        $sets = $this->setCollectionFactory->create()->setEntityTypeFilter(
            $resource->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header'           => __('Attribute Set'),
                'index'            => 'attribute_set_id',
                'type'             => 'options',
                'options'          => $sets,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'           => __('Status'),
                'index'            => 'status',
                'type'             => 'options',
                'options'          => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header'           => __('Visibility'),
                'index'            => 'visibility',
                'type'             => 'options',
                'options'          => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header'           => __('SKU'),
                'index'            => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'price',
            [
                'header'           => __('Price'),
                'type'             => 'currency',
                'currency_code'    => (string)$this->_scopeConfig->getValue(
                    Currency::XML_PATH_CURRENCY_BASE,
                    ScopeInterface::SCOPE_STORE
                ),
                'index'            => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        return $this;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $selected = $this->getProductTemplate()->getProductData();
        return $selected;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $selected = $this->getProductTemplate()->getProductData();

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }
    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }
    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/productsGrid',
            [
                'template_id' => $this->getProductTemplate()->getId(),
                'store_id'    => $this->getProductTemplate()->getStoreId(),
                'type_id'     => $this->getProductTemplate()->getTypeId()
            ]
        );
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in'=>$productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin'=>$productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Products');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return !($this->getProductTemplate()->isAssignForIndividualItems($this->getProductTemplate()->getAssignType()));
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mageworx_seoxtemplates/templateproduct/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Retrieve store id from request
     *
     * @return int
     */
    protected function getTemplateStoreId()
    {
        $templateParams = $this->getRequest()->getParam('template');

        if ($templateParams && array_key_exists('store_id', $templateParams) && $templateParams['store_id'] !== '') {
            return $templateParams['store_id'];
        }

        return $this->getProductTemplate()->getStoreId();
    }

    /**
     * Retrieve type id from request
     *
     * @return int
     */
    protected function getTemplateTypeId()
    {
        $templateParams = $this->getRequest()->getParam('template');

        if ($templateParams && array_key_exists('type_id', $templateParams) && $templateParams['type_id'] !== '') {
            return $templateParams['type_id'];
        }

        return $this->getProductTemplate()->getTypeId();
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template
     */
    protected function getProductTemplate()
    {
        return $this->registry->registry('mageworx_seoxtemplates_template');
    }
}
