<?php
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Landingpage\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as Status;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as DataHelper;


class LandingPages extends ExtendedGrid implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * LandingPages constructor.
     *
     * @param Status $status
     * @param Registry $registry
     * @param Context $context
     * @param DataHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        Status $status,
        Registry $registry,
        Context $context,
        DataHelper $backendHelper,
        array $data = []
    ) {
        $this->status = $status;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('landingpage_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getLandingPageTemplate()->getId()) {
            $this->setDefaultFilter(['in_landingpages' => 1]);
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
        $collection = $this->getLandingPageTemplate()->getLandingPages();

        $excludeLandingPagesIds = $this->getLandingPageTemplate()->getLandingPageIdsAssignedForAnalogTemplate();
        if (!empty($excludeLandingPagesIds)) {
            $collection->getSelect()->where('main_table.landingpage_id NOT IN (?)', $excludeLandingPagesIds);
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
            'in_landingpages',
            [
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_landingpages',
                'values'            => $this->_getSelectedLandingPages(),
                'align'             => 'center',
                'index'             => 'landingpage_id'
            ]
        );
        $this->addColumn(
            'landingpage_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'landingpage_id',
                'type'             => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header'           => __('Title'),
                'index'            => 'title',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
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

        return $this;
    }

    /**
     * Retrieve selected landing pages
     *
     * @return array
     */
    protected function _getSelectedLandingPages()
    {
        $selected = $this->getLandingPageTemplate()->getLandingPagesData();
        return $selected;
    }

    /**
     * Retrieve selected landing pages
     *
     * @return array
     */
    public function getSelectedLandingPages()
    {
        $selected = $this->getLandingPageTemplate()->getLandingPagesData();

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }
    /**
     * @param \Magento\Framework\Object $item
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
            '*/*/landingpagesGrid',
            [
                'template_id' => $this->getLandingPageTemplate()->getId(),
                'store_id'    => $this->getLandingPageTemplate()->getStoreId(),
                'type_id'     => $this->getLandingPageTemplate()->getTypeId()
            ]
        );
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_landingpages') {
            $landingPagesIds = $this->_getSelectedLandingPages();
            if (empty($landingPagesIds)) {
                $landingPagesIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('landingpage_id', ['in' => $landingPagesIds]);
            } else {
                if ($landingPagesIds) {
                    $this->getCollection()->addFieldToFilter('landingpage_id', ['nin' => $landingPagesIds]);
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
        return __('Landing Pages');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return !($this->getLandingPageTemplate()->isAssignForIndividualItems($this->getLandingPageTemplate()->getAssignType()));
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
        return $this->getUrl('mageworx_seoxtemplates/templatelandingpage/landingpages', ['_current' => true]);
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

        return $this->getLandingPageTemplate()->getStoreId();
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

        return $this->getLandingPageTemplate()->getTypeId();
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template
     */
    protected function getLandingPageTemplate()
    {
        return $this->registry->registry('mageworx_seoxtemplates_template');
    }
}
