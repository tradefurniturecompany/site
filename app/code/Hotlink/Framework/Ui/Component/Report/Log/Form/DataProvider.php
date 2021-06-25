<?php
namespace Hotlink\Framework\Ui\Component\Report\Log\Form;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $sausage = false;   // declared in parent
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Hotlink\Framework\Model\ResourceModel\Report\Log\CollectionFactory $logCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        // Magento\Ui\Component\Form needs collection to be an instance as it needs to apply filters on it
        $this->_setCollection( $logCollectionFactory->create() );
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    protected function _setCollection( $value )
    {
        $this->sausage = $value;
        return $this;
    }

    public function getCollection()
    {
        return $this->sausage;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        // Magento\Ui\Component\Form applies filder: record_id=[id in url]
        $items = $this->getCollection()->getItems();

        foreach ($items as $log) {
            // Magento\Ui\Component\Form expects record_id to be among the keys of the result
            $this->loadedData[ $log->getRecordId() ] = $log->getData();
        }

        return $this->loadedData;
    }
}
