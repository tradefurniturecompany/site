<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use MageWorx\SeoExtended\Model\CategoryFilter as CategoryFilterModel;
use Magento\Framework\Event\ManagerInterface;

class CategoryFilter extends AbstractDb
{
    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $_uniqueFields = [
        [
            'field' => ['attribute_id', 'category_id', 'store_id', 'attribute_option_id'],
            'title' => 'Attribute ID, Attribute Option ID, Category ID and Store ID combination'
        ]
    ];

    /**
     * CategoryFilter constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ManagerInterface $eventManager
    ) {
        $this->storeManager = $storeManager;
        $this->eventManager = $eventManager;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoextended_category', 'id');
    }
}
