<?php
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Helper;

use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Backend\Helper\Data as DataHelper;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Escaper;

/**
 * @method mixed getValue()
 */

class Category extends Multiselect
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Backend data
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $_coreRegistry,
        CollectionFactory $collectionFactory,
        DataHelper $backendData,
        LayoutInterface $layout,
        EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        array $data = []
    ) {
        $this->_coreRegistry = $_coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->backendData = $backendData;
        $this->layout = $layout;
        $this->jsonEncoder = $jsonEncoder;
        $this->authorization = $authorization;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Magento_Catalog::categories');
        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = [];

        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected function _getCategoriesCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     * @return string
     */
    public function getAfterElementHtml()
    {
        if (!$this->isAllowed()) {
            return '';
        }
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->jsonEncoder->encode($this->_getSelectorOptions());

        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        require(["jquery", "mage/mage"], function($) {
            $('#{$htmlId}-suggest').mage('MageWorx_SeoXTemplates/mageworx/tree-suggest', {$selectorOptions});
        });
    </script>
HTML;

        return $return . '<p>In tree mode, don\'t select the disabled categories.</p>' ;
    }

    /**
     * Get selector options
     * @return array
     */
    protected function _getSelectorOptions()
    {
        $template = $this->_coreRegistry->registry('mageworx_seoxtemplates_template');
        $templateId = $template->getId();
        $storeId    = $template->getStoreId();
        $typeId     = $template->getTypeId();

        return [
            'source' => $this->backendData->getUrl(
                'mageworx_seoxtemplates/templatecategory/suggestCategories',
                ['template_id' => $templateId, 'store_id' => $storeId, 'type_id'=> $typeId]
            ),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        ];
    }

    /**
     * Whether permission is granted
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Magento_Catalog::categories');
    }
}
