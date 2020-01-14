<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

use Magento\Backend\App\Action\Context;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirectFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect as DpRedirectController;
use Magento\Framework\Registry;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;

class InlineEdit extends DpRedirectController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param DpRedirectFactory $dpRedirectFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        DpRedirectFactory $dpRedirectFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $dpRedirectFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the sent data.')],
                    'error'    => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $redirectId) {
            /** @var DpRedirectfactory $redirect */
            $redirect = $this->dpRedirectFactory->create()->load($redirectId);
            try {
                $redirectData = $this->filterData($postItems[$redirectId]);
                $redirect->addData($redirectData);
                $redirect->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithRedirectId($redirect, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRedirectId($redirect, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRedirectId(
                    $redirect,
                    __('Something went wrong while saving the redirect.')
                );
                $error      = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error'    => $error
            ]
        );
    }

    /**
     * Add redirect id to error message
     *
     * @param DpRedirect $redirect
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRedirectId(DpRedirect $redirect, $errorText)
    {
        return '[Redirect ID: ' . $redirect->getId() . '] ' . $errorText;
    }
}
