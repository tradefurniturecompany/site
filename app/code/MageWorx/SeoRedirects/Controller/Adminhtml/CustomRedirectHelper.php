<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml;

use MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory;
use MageWorx\SeoRedirects\Controller\RegistryConstants;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

class CustomRedirectHelper
{
    /**
     * @var \MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface
     */
    protected $customRedirectRepository;

    /**
     * @var CustomRedirectFactory
     */
    protected $customRedirectFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * CategoryFilterHelper constructor
     *
     * @param CustomRedirectRepositoryInterface $customRedirectRepository
     * @param CustomRedirectFactory $customRedirectFactory
     * @param RequestInterface $request
     * @param Registry $coreRegistry
     */
    public function __construct(
        CustomRedirectRepositoryInterface $customRedirectRepository,
        CustomRedirectFactory $customRedirectFactory,
        RequestInterface $request,
        Registry $coreRegistry
    ) {
        $this->redirectRepository = $customRedirectRepository;
        $this->redirectFactory    = $customRedirectFactory;
        $this->request            = $request;
        $this->coreRegistry       = $coreRegistry;
    }

    /**
     * @return \MageWorx\SeoRedirects\Api\Data\CategoryFilterInterface
     */
    public function initRedirect($customRedirectId = null)
    {
        $customRedirectId = $customRedirectId ? $customRedirectId : $this->request->getParam('redirect_id');

        if ($customRedirectId) {
            $customRedirect = $this->redirectRepository->getById($customRedirectId);
        } else {
            $customRedirect = $this->redirectFactory->create();
        }

        $this->coreRegistry->register(RegistryConstants::CURRENT_REDIRECT_CONSTANT, $customRedirect);

        return $customRedirect;
    }
}
