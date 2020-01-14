<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Controller\Adminhtml\Review;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use MageWorx\Info\Helper\Data;

class Send extends \Magento\Backend\App\Action
{

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \MageWorx\Info\Model\MetaPackageList
     */
    protected $metaPackageList;

    /**
     * Send constructor.
     *
     * @param \MageWorx\Info\Model\MetaPackageList $metaPackageList
     * @param Data $helper
     * @param Context $context
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        \MageWorx\Info\Model\MetaPackageList $metaPackageList,
        Data $helper,
        Context $context,
        RawFactory $resultRawFactory
    ) {
        parent::__construct(
            $context
        );
        $this->metaPackageList  = $metaPackageList;
        $this->helper           = $helper;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost()->toArray();
        if (isset($data['ext_code'])) {
            $data['from_url'] = str_replace(['https://', 'http://'], '', $this->helper->getStoreUrl());
            $data['version']  = $this->metaPackageList->getInstalledVersion($data['ext_code']);
            $result           = $this->helper->sendReviewData($data);
        } else {
            $result = false;
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));

        return $response;
    }
}
