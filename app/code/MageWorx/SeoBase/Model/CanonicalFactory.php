<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 * @see \MageWorx\SeoBase\Model\Canonical
 */
class CanonicalFactory
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
         $this->map = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return \MageWorx\SeoBase\Model\CanonicalInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            $instance = $this->objectManager->create('\MageWorx\SeoBase\Model\Canonical\Simple', $arguments);
        }

        if (!$instance instanceof \MageWorx\SeoBase\Model\CanonicalInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\SeoBase\Model\CanonicalInterface'
            );
        }
        return $instance;
    }
}
