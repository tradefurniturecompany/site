<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Generator;

use Magento\Framework\ObjectManagerInterface;
use MageWorx\XmlSitemap\Helper\Data as Helper;

/**
 * {@inheritdoc}
 */
abstract class AbstractGenerator implements \MageWorx\XmlSitemap\Model\GeneratorInterface
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $storeBaseUrl;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var int
     */
    protected $counter = 0;

    const COLLECTION_LIMIT = 500;

    /**
     * AbstractGenerator constructor.
     * @param Helper $helper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Helper $helper,
        ObjectManagerInterface $objectManager
    ) {
        $this->helper        = $helper;
        $this->objectManager = $objectManager;
    }

    /**
     * Return count of urls
     *
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Return generator code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return generator code
     *
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $model
     * @return string
     */
    protected function getItemChangeDate($model)
    {
        $upTime = $model->getUpdatedAt();
        if ($upTime == '0000-00-00 00:00:00') {
            $upTime = $model->getCreatedAt();
        }

        $upTime = substr($upTime, 0, 10);

        if (!$upTime) {
            $upTime = $this->helper->getCurrentDate();
        }

        return $upTime;
    }
}