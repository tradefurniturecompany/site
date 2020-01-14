<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab\Rates\Grid;

/**
 * Columns for Shipping Rate Grid
 */
class ColumnSet extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory,
        \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals,
        \Magento\Backend\Model\Widget\Grid\Totals $totals,
        \Amasty\ShippingTableRates\Helper\Data $helper,
        array $data
    ) {
        $this->helper = $helper;
        parent::__construct($context, $generatorFactory, $subtotals, $totals, $data);
    }

    protected function _prepareLayout()
    {
        $this->addColumn('country', [
            'header' => __('Country'),
            'index' => 'country',
            'type' => 'options',
            'options' => $this->helper->getCountries(),
        ]);

        $this->addColumn('state', [
            'header' => __('State'),
            'index' => 'state',
            'type' => 'options',
            'options' => $this->helper->getStates(),
        ]);

        $this->addColumn('city', [
            'header' => __('City'),
            'index' => 'city',
            'type' => 'text',
        ]);

        $this->addColumn('zip_from', [
            'header' => __('Zip From'),
            'index' => 'zip_from',
        ]);

        $this->addColumn('zip_to', [
            'header' => __('Zip To'),
            'index' => 'zip_to',
        ]);

        $this->addColumn('price_from', [
            'header' => __('Price From'),
            'index' => 'price_from',
        ]);

        $this->addColumn('price_to', [
            'header' => __('Price To'),
            'index' => 'price_to',
        ]);

        $this->addColumn('weight_from', [
            'header' => __('Weight From'),
            'index' => 'weight_from',
        ]);

        $this->addColumn('weight_to', [
            'header' => __('Weight To'),
            'index' => 'weight_to',
        ]);

        $this->addColumn('qty_from', [
            'header' => __('Qty From'),
            'index' => 'qty_from',
        ]);

        $this->addColumn('qty_to', [
            'header' => __('Qty To'),
            'index' => 'qty_to',
        ]);

        $this->addColumn('shipping_type', [
            'header' => __('Shipping Type'),
            'index' => 'shipping_type',
            'type' => 'options',
            'options' => $this->helper->getTypes(),
        ]);

        $this->addColumn('cost_base', [
            'header' => __('Rate'),
            'index' => 'cost_base',
        ]);

        $this->addColumn('cost_percent', [
            'header' => __('PPP'),
            'index' => 'cost_percent',
        ]);

        $this->addColumn('cost_product', [
            'header' => __('FRPP'),
            'index' => 'cost_product',
        ]);

        $this->addColumn('cost_weight', [
            'header' => __('FRPUW'),
            'index' => 'cost_weight',
        ]);

        $this->addColumn('time_delivery', [
            'header' => __('Estimated Delivery (days)'),
            'index' => 'time_delivery',
        ]);

        $this->addColumn('name_delivery', [
            'header' => __('Name delivery'),
            'index' => 'name_delivery',
        ]);

        $link = $this->getUrl('amstrates/rates/delete') . 'id/$id';
        $this->addColumn('action', [
            'header' => __('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getVid',
            'actions' => [
                [
                    'caption' => __('Delete'),
                    'url' => $link,
                    'field' => 'id',
                    'confirm' => __('Are you sure?')
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ]);
        return parent::_prepareLayout();
    }

    public function addColumn($title, $data)
    {
        $column = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Grid\Column::class, $title)
            ->addData($data);
        $this->setChild($title, $column);
    }

    public function getRowUrl($item)
    {
        return $this->getUrl('amstrates/rates/edit', ['id' => $item->getId()]);
    }
}
