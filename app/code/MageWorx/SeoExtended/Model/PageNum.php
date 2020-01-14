<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use MageWorx\SeoExtended\Helper\Data as HelperData;

abstract class PageNum implements \MageWorx\SeoExtended\Model\PageNumInterface
{
    abstract public function getCurrentPageNum();

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
     * @var string
     */
    protected $pageVarName;

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
        $fullActionName,
        $pageVarName
    ) {
        $this->helperData     = $helperData;
        $this->request        = $request;
        $this->fullActionName = $fullActionName;
        $this->pageVarName    = $pageVarName;
    }

    /**
     *
     * @return int
     */
    protected function getPagerNumFromRequest()
    {
        return (int)$this->request->getParam($this->pageVarName);
    }
}
