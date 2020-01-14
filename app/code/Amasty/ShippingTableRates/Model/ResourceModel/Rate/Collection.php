<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\ResourceModel\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Rates Resource Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_scopeConfig;

    protected $_helper;

    protected function _construct()
    {
        $this->_init(
            \Amasty\ShippingTableRates\Model\Rate::class,
            \Amasty\ShippingTableRates\Model\ResourceModel\Rate::class
        );
    }

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\ShippingTableRates\Helper\Data $helper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @param array $methodIds
     * @param bool $resetColumns
     * @param array|null $params
     *
     * @return array
     */
    public function getRatesWithFilters($methodIds, $resetColumns = false, $params = null)
    {
        $this->addMethodFilters($methodIds);

        if ($resetColumns) {
            $this->removeAllFieldsFromSelect();
            $this->getSelect()->columns(['method_id', 'shipping_type']);
        }

        if ($params) {
            list($request, $totals, $shippingType, $allowFreePromo) = $params;
            $this->addAddressFilters($request);
            $this->addTotalsFilters($totals, $shippingType, $request, $allowFreePromo);
        }

        return $this->getConnection()->fetchAssoc($this->getSelect());
    }

    /**
     * reset collection
     */
    public function reset()
    {
        $this->clear();
        $this->resetData();
        $this->getSelect()->reset(\Zend_Db_Select::WHERE);
    }

    /**
     * @param array $methodIds
     *
     * @return $this
     */
    private function addMethodFilters($methodIds)
    {
        $this->addFieldToFilter('method_id', ['in' => $methodIds]);

        return $this;
    }

    /**
     * @param RateRequest $request
     *
     * @return $this
     */
    private function addAddressFilters(RateRequest $request)
    {
        $this->addFieldToFilter(
            'country',
            [
                [
                    'like' => $request->getDestCountryId(),
                ],
                [
                    'eq' => '0',
                ],
                [
                    'eq' => '',
                ],
            ]
        );

        $this->addFieldToFilter(
            'state',
            [
                [
                    'like' => $request->getDestRegionId(),
                ],
                [
                    'eq' => '0',
                ],
                [
                    'eq' => '',
                ],
            ]
        );

        $this->addFieldToFilter(
            'city',
            [
                [
                    'like' => $request->getDestCity(),
                ],
                [
                    'eq' => '0',
                ],
                [
                    'eq' => '',
                ],
            ]
        );

        $inputZip = $request->getDestPostcode();
        if ($this->_scopeConfig->getValue('carriers/amstrates/numeric_zip')) {
            if ($inputZip == '*') {
                $inputZip = '';
            }
            $zipData = $this->_helper->getDataFromZip($inputZip);
            $zipData['district'] = $zipData['district'] !== '' ? (int)$zipData['district'] : -1;

            $this->getSelect()
                ->where('`num_zip_from` <= ? OR `zip_from` = ""', $zipData['district'])
                ->where('`num_zip_to` >= ? OR `zip_to` = ""', $zipData['district']);

            if (!empty($zipData['area']) && preg_match('~^[\p{L}\p{Z}-]+$~u', $zipData['area'])) {
                $this->addFieldToFilter(
                    'zip_from',
                    [
                        [['regexp' => '^' . $zipData['area'] . '[0-9]+'], ['eq' => '']],
                    ]
                );
            }

            //to prefer rate with zip
            $this->setOrder('num_zip_from', 'DESC');
            $this->addOrder('num_zip_to', 'DESC');
        } else {
            $this->getSelect()->where("? LIKE zip_from OR zip_from = ''", $inputZip);
        }

        return $this;
    }

    /**
     * @param array $totals
     * @param int $shippingType
     * @param RateRequest $request
     * @param int $allowFreePromo
     *
     * @return $this
     */
    private function addTotalsFilters($totals, $shippingType, RateRequest $request, $allowFreePromo)
    {
        if (!($request->getFreeShipping() && $allowFreePromo)) {
            $this->addFieldToFilter('price_from', ['lteq' => $totals['not_free_price']]);
            $this->addFieldToFilter('price_to', ['gteq' => $totals['not_free_price']]);
        }
        $this->addFieldToFilter('weight_from', ['lteq' => $totals['not_free_weight']]);
        $this->addFieldToFilter('weight_to', ['gteq' => $totals['not_free_weight']]);
        $this->addFieldToFilter('qty_from', ['lteq' => $totals['not_free_qty']]);
        $this->addFieldToFilter('qty_to', ['gteq' => $totals['not_free_qty']]);
        $this->addFieldToFilter(
            'shipping_type',
            [
                [
                    'eq' => $shippingType,
                ],
                [
                    'eq' => '',
                ],
                [
                    'eq' => '0',
                ],
            ]
        );

        return $this;
    }
}
