<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class for robots
 *
 * @see \MageWorx\SeoBase\Model\Robots
 */
class RobotsFactory
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
     * Create new instance
     *
     * @param string $param
     * @param array $arguments
     * @return \MageWorx\SeoBase\Model\RobotsInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            $instance = $this->objectManager->create('\MageWorx\SeoBase\Model\Robots\Simple', $arguments);
        }

        if (!$instance instanceof \MageWorx\SeoBase\Model\RobotsInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\SeoBase\Model\RobotsInterface'
            );
        }
        return $instance;
    }
}
