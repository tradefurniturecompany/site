<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 *
 * @see \MageWorx\SeoBase\Model\Canonical
 */
class RedirectWizardByDeletedUrlFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $map
     */

    public function __construct(
        ObjectManager $objectManager,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map           = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlAbstract
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        $instance = null;

        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        }

        if (!$instance instanceof \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlAbstract) {
            throw new \UnexpectedValueException(
                'Class ' . get_class(
                    $instance
                ) . ' should be an instance of \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlAbstract'
            );
        }

        return $instance;
    }
}
