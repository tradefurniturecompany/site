<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 * @see \MageWorx\SeoExtended\Model\MetaUpdater
 */
class MetaUpdaterFactory
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
     * @param $param
     * @param array $arguments
     * @return \MageWorx\SeoExtended\Model\MetaUpdaterInterface|null
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            return null;
        }

        if (!$instance instanceof \MageWorx\SeoExtended\Model\MetaUpdaterInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\SeoExtended\Model\MetaUpdaterInterface'
            );
        }
        return $instance;
    }
}
