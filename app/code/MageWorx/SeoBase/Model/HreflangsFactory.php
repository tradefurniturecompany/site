<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 * @see \MageWorx\SeoBase\Model\Hreflangs
 */
class HreflangsFactory
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
     * @return \MageWorx\SeoBase\Model\HreflanglInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            return null;
        }

        if (!$instance instanceof \MageWorx\SeoBase\Model\HreflangsInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\SeoBase\Model\HreflangsInterface'
            );
        }
        return $instance;
    }
}
