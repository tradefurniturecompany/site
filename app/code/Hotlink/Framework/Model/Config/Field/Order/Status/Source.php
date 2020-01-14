<?php
namespace Hotlink\Framework\Model\Config\Field\Order\Status;

class Source implements \Magento\Framework\Option\ArrayInterface
{

    protected $configFactory;

    public function __construct(
        \Magento\Sales\Model\Order\ConfigFactory $configFactory
    )
    {
        $this->configFactory = $configFactory;
    }

    public function toOptionArray()
    {
        $values = [];
        $statuses = $this->configFactory->create()->getStatuses();
        foreach ( $statuses as $code => $label )
            {
                $values[] = [
                    'label' => __( $label ),
                    'value' => $code
                ];
            }
        return $values;
    }

    public function toArray()
    {
        return $this->salesOrderConfigFactory->create()->getStatuses();
    }
}
