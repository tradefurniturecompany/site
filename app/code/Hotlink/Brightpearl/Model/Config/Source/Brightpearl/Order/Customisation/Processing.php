<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Customisation;

class Processing implements \Magento\Framework\Option\ArrayInterface
{

    protected $_data = [ 'skip' => 'Skip',
                         'warn' => 'Warn',
                         'stop' => 'Stop'
    ];

    function toOptionArray()
    {
        $options = [];
        foreach ( $this->toArray() as $key => $label )
            {
                $options[] = [ 'value' => $key, 'label' => $label ];
            }
        return $options;
    }

    function toArray()
    {
        $result = [];
        foreach ( $this->_data as $key => $label )
            {
                $result[ $key ] = __( $label );
            }
        return $result;
    }

}
