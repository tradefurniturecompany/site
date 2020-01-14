<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab\Rates;

/**
 * Grid of Rates initialization
 */
class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate\CollectionFactory
     */
    private $rateCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Amasty\ShippingTableRates\Model\ResourceModel\Rate\CollectionFactory $rateCollectionFactory,
        array $data
    ) {
        $this->rateCollectionFactory = $rateCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Rate\Collection $collection */
        $collection = $this->rateCollectionFactory->create();
        $id = $this->getRequest()->getParam('id');
        $collection->addFieldToFilter('method_id', $id);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}
