<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use Magento\Backend\App\Action\Context;
use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink as CrosslinkController;
use Magento\Framework\Registry;
use MageWorx\SeoCrossLinks\Model\Crosslink;

class InlineEdit extends CrosslinkController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param CrosslinkFactory $crosslinkFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        CrosslinkFactory $crosslinkFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $crosslinkFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $crosslinkId) {
            /** @var \MageWorx\SeoCrosslinks\Model\Crosslinks $crosslink */
            $crosslink = $this->crosslinkFactory->create()->load($crosslinkId);
            try {
                $crosslinkData = $this->filterData($postItems[$crosslinkId]);
                $crosslink->addData($crosslinkData);
                $crosslink->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getError($crosslink, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getError($crosslink, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getError(
                    $crosslink,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add crosslink id to error message
     *
     * @param Crosslink $crosslink
     * @param string $errorText
     * @return string
     */
    protected function getError(Crosslink $crosslink, $errorText)
    {
        return '[Crosslink ID: ' . $crosslink->getId() . '] ' . $errorText;
    }
}
