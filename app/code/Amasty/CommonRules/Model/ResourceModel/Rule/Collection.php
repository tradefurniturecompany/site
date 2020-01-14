<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\ResourceModel\Rule;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->coreDate = $coreDate;
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, null);
    }

    /**
     * @param $address
     * @return $this
     */
    public function addAddressFilter($address)
    {
        $groupId = 0;
        $customerId = $address->getQuote()->getCustomerId();

        if ($customerId) {
            $groupId = $this->customerRepositoryInterface->getById($customerId)->getGroupId();
        }

        $this->addActiveFilter()
            ->addCustomerGroupFilter($groupId)
            ->addStoreFilter($this->getStoreId($address))
            ->addDaysFilter();

        return $this;
    }

    /**
     * @param $address
     * @return mixed
     */
    protected function getStoreId($address)
    {
        $quote = $address->getQuote();

        return ($this->state->getAreaCode() == FrontNameResolver::AREA_CODE && $quote) ? $quote->getStoreId()
            : $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $storeId = (int)$storeId;
        $this->addFieldToFilter(['stores', 'stores'], [
            [''],
            ['finset' => $storeId]
        ]);

        return $this;
    }

    /**
     * @param $groupId
     * @return $this
     */
    public function addCustomerGroupFilter($groupId)
    {
        $groupId = (int)$groupId;
        $this->addFieldToFilter(['cust_groups', 'cust_groups'], [
            [''],
            ['finset' => $groupId]
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);

        return $this;
    }

    /**
     * @return $this
     */
    public function addDaysFilter()
    {
        $this->addFieldToFilter(['days', 'days', 'days'], [
            ['null' => true],
            [''],
            ['finset' => $this->coreDate->date('N')]
        ]);

        $localeDate = $this->localeDate->date();
        $timeStamp = $localeDate->format('H') * 100 + $localeDate->format('i') + 1;

        $this->getSelect()->where('(time_from IS NULL) OR (time_to IS NULL)
        OR time_from="" OR time_from="0" OR time_to="" OR time_to="0" OR
        (time_from < ' . $timeStamp . ' AND time_to > ' . $timeStamp . ') OR
        (time_from < ' . $timeStamp . ' AND time_to < time_from) OR
        (time_to > ' . $timeStamp . ' AND time_to < time_from)');

        return $this;
    }
}
