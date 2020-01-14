<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class StatusOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const ACTIVE  = 1;
    const INACTIVE = 0;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                self::ACTIVE => __('Active'),
                self::INACTIVE => __('Inactive')
            ];
        }

        return $this->options;
    }
}
