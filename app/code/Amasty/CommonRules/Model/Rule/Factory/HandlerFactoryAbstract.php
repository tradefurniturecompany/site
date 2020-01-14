<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Factory;

abstract class HandlerFactoryAbstract implements HandleFactoryInterface
{
    /**
     * @var array
     */
    protected $handlers;

    /**
     * @param string $type
     * @return array
     */
    public function create($type = self::CUSTOMER_HANDLE)
    {
        return $this->getConditionsByType($type);
    }

    /**
     * @param $type
     * @return bool|mixed
     */
    public function getHandlerByType($type)
    {
        return isset($this->handlers[$type]) ? $this->handlers[$type] : false;
    }

    /**
     * @param $type
     * @return array
     */
    abstract protected function getConditionsByType($type);
}

