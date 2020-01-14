<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use MageWorx\SeoExtended\Helper\Data as HelperData;

abstract class FiltersConvertor implements \MageWorx\SeoExtended\Model\FiltersConvertorInterface
{
    /**
     * {@inheritDoc}
     */
    abstract public function getStringByFilters();

    /**
     * @var \MageWorx\SeoExtended\Helper\Data
     */
    protected $helperData;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @param HelperData $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $fullActionName
     * @param string $pageVarName
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Framework\App\RequestInterface $request,
        $fullActionName
    ) {
        $this->helperData     = $helperData;
        $this->request        = $request;
        $this->fullActionName = $fullActionName;
    }
}
