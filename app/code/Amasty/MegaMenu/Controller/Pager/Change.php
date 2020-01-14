<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Controller\Pager;

/**
 * Class Change
 * @package Amasty\MegaMenu\Controller\Pager
 */
class Change extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $widgetData = $this->getRequest()->getParam('widget_data');
        $resultJson = $this->resultJsonFactory->create();

        $block = $this->getBlock($widgetData);
        $result['block'] = $block->toHtml();

        return $resultJson->setData($result);
    }

    /**
     * @param array $widgetData
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getBlock(array $widgetData)
    {
        $layout = $this->layoutFactory->create();

        return $layout->createBlock(
            \Amasty\MegaMenu\Block\Product\ProductsSlider::class,
            $widgetData['name'],
            ['data' => $widgetData['data']]
        );
    }
}
