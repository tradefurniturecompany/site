<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 *
 * @see \MageWorx\SeoExtended\Model
 */
class PageNumFactory
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
     * @return \MageWorx\SeoExtended\Model\PageNumInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            $instance = null;
        }

        if ($instance && !($instance instanceof \MageWorx\SeoExtended\Model\PageNumInterface)) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\SeoExtended\Model\PageNumInterface'
            );
        }
        return $instance;
    }
}
