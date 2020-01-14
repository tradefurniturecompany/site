<?php
namespace Hotlink\Framework\Model\System\Config\Form\Field\Scalar;

class Backend extends \Magento\Framework\App\Config\Value
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function setValue( $value )
    {
        if ( $value instanceof \Magento\Framework\App\Config\Element )
            {
                // Loading config with a value that's never been saved before - respect the default
                $value = ( string ) $value;
            }
        if ( is_array( $value ) )
            {
                $value = implode( ' ', $value );
            }
        $this->setData( 'value', $value );
        return $this;
    }

    public function afterLoad()
    {
        return $this;
    }
}