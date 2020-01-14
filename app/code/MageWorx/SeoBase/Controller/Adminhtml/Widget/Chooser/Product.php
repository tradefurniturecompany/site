<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Widget\Chooser;

use MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

class Product extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /** @var string */
    protected $categoryChooserClass = 'Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser';

    /** @var string */
    protected $productChooserContainerClass = 'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser\Container';

    /** @var string */
    protected $productChooserClass = 'MageWorx\SeoBase\Block\Adminhtml\Widget\Chooser\Product';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory    = $layoutFactory;
    }

    /**
     * Chooser Source action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $layout            = $this->layoutFactory->create();
        $productsGridBlock = $this->createProductGrid($layout);

        $gridHtml         = $productsGridBlock->toHtml();
        $categoryTreeHtml = '';

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $layout->createBlock(
                $this->categoryChooserClass,
                '',
                [
                    'data' => [
                        'id'                  => $this->getRequest()->getParam('uniq_id') . 'Tree',
                        'node_click_listener' => $productsGridBlock->getCategoryClickListenerJs(),
                        'with_empty_node'     => true,
                    ]
                ]
            );

            $categoryTreeHtml = $categoriesTree->toHtml();
        }

        $html = $this->getFinalHtml($layout, $gridHtml, $categoryTreeHtml);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return mixed
     */
    protected function createProductGrid($layout)
    {
        $data = [
            'id'              => $this->getRequest()->getParam('uniq_id'),
            'category_id'     => $this->getRequest()->getParam('category_id'),
            'use_massaction'  => $this->getRequest()->getParam('use_massaction', false),
            'product_type_id' => $this->getRequest()->getParam('product_type_id', null)
        ];

        return $layout->createBlock($this->productChooserClass, '', ['data' => $data]);
    }

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param string $gridHtml
     * @param string $categoriesTreeHtml
     * @return mixed
     */
    protected function getFinalHtml($layout, $gridHtml, $categoriesTreeHtml)
    {
        return $layout->createBlock($this->productChooserContainerClass)
                      ->setTreeHtml($categoriesTreeHtml)
                      ->setGridHtml($gridHtml)
                      ->toHtml();
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(Customcanonical::ADMIN_RESOURCE);
    }
}
