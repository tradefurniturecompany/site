<?php
namespace Hotlink\Brightpearl\Model\Config\Field\Serialize;

class Backend extends \Magento\Framework\App\Config\Value
{

    /**
     * @var \Hotlink\Brightpearl\Helper\Data
     */
    protected $brightpearlHelper;

    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Hotlink\Brightpearl\Helper\Data $brightpearlHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->brightpearlHelper = $brightpearlHelper;

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

    protected function _afterLoad()
    {
        $value = $this->brightpearlHelper->getUnserializedOptions( $this->getValue() );
        $this->setValue( $value );
    }

    function save()
    {
        $value = $this->brightpearlHelper->getSerializedOptions( $this->getValue() );
        $this->setValue( $value );

        return parent::save();
    }
}
