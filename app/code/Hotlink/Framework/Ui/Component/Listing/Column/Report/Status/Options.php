<?php
namespace Hotlink\Framework\Ui\Component\Listing\Column\Report\Status;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options;

    public function toOptionArray()
    {
        if ($this->options === null) {
            $rawOptions = \Hotlink\Framework\Model\Report::getStatusOptions();

            $options = array_map( function($v, $i){
                    return [ 'value' => $i, 'label' => $v];
                },
                $rawOptions,
                array_keys($rawOptions) );

            $this->options = $options;
        }
        return $this->options;
    }
}
