<?php
namespace Hotlink\Framework\Model\Report;

class Log extends \Magento\Framework\Model\AbstractModel
{

    protected $collectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Model\ResourceModel\Report\Log\CollectionFactory $collectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function _construct()
    {
        $this->_init('Hotlink\Framework\Model\ResourceModel\Report\Log', 'record_id');
    }

    public function exists()
    {
        $record_id = $this->getRecordId();
        if ( empty($record_id) ) {
            return false;
        }

        $collection = $this->collectionFactory->create();
        $collection->getSelect()->where( 'record_id = ' . $record_id );
        $count = $collection->getSize();
        return ( $count != 0 );
    }
}
