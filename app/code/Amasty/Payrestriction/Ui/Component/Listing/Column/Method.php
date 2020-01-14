<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Ui\Component\Listing\Column;

class Method implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options;
    protected $poolOptionProvider;

    public function __construct(
        \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider
    )
    {
        $this->poolOptionProvider = $poolOptionProvider;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->poolOptionProvider->getOptionsByProviderCode('payment_method');
        }

        return $this->options;
    }
}
