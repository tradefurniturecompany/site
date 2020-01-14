<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Ui\Component\Listing\Column;

class Group implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options;
    protected $group;

    public function __construct(
        \Magento\Customer\Model\Customer\Attribute\Source\Group $group
    )
    {
        $this->group = $group;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->group->toOptionArray();
            $this->options[] = [
                'value' => 'all',
                'label' => __('Restricts For All')
            ];
        }

        return $this->options;
    }
}
