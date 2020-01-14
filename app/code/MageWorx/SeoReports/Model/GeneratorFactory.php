<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 *
 * @see \MageWorx\SeoReports\Model\GeneratorInterface
 */
class GeneratorFactory
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
     * @return \MageWorx\SeoReports\Model\GeneratorInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            return null;
        }

        if (!$instance instanceof \MageWorx\SeoReports\Model\GeneratorInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class(
                    $instance
                ) . ' should be an instance of \MageWorx\SeoReports\Model\GeneratorInterface'
            );
        }

        return $instance;
    }
}
