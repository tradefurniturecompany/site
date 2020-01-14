<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider;

class Pool
{
    /**
     * @var \Magento\Framework\Data\OptionSourceInterface[]
     */
    protected $optionProviders;

    /**
     * PoolOptionProvider constructor.
     * @param array $optionProviders
     */
    public function __construct(array $optionProviders)
    {
        $this->optionProviders = $optionProviders;
    }

    /**
     * List of registered option providers
     *
     * @return \Magento\Framework\Data\OptionSourceInterface[]
     */
    public function getOptionProviders()
    {
        return $this->optionProviders;
    }

    /**
     * @param $providerCode
     * @return array|null
     */
    public function getOptionsByProviderCode($providerCode)
    {
        return isset($this->optionProviders[$providerCode])
            ? $this->optionProviders[$providerCode]->toOptionArray() : null;
    }
}
