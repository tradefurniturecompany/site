<?php
namespace Magesales\Shippingtable\Model\Config\Source;
class Ziptype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $vals = [
            '0' => __('Numeric'),
            '1' => __('String'),
        ];

        $options = [];
        foreach ($vals as $k => $v)
            $options[] = [
                    'value' => $k,
                    'label' => $v
            ];
        
        return $options;
    }
}