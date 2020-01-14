<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templateproduct as TemplateController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\ProductFactory as TemplateProductFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\LayoutFactory;

class Products extends TemplateController
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param Registry $registry
     * @param TemplateProductFactory $templateProductFactory
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Registry $registry,
        TemplateProductFactory $templateProductFactory,
        Context $context
    ) {
    
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($registry, $templateProductFactory, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initTemplateProduct();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \MageWorx\SeoXTemplates\Block\Adminhtml\Template\Edit\Tab\Products $productsBlock */
        $productsBlock = $resultLayout->getLayout()->getBlock('template_edit_tab_product');
        if ($productsBlock) {
            $productsBlock->setTemplateProducts($this->getRequest()->getPost('template_products', null));
        }
        return $resultLayout;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
