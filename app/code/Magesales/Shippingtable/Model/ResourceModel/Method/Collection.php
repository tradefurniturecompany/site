<?php
namespace Magesales\Shippingtable\Model\ResourceModel\Method;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magesales\Shippingtable\Model\Method', 'Magesales\Shippingtable\Model\ResourceModel\Method');
	}
    
    public function addStoreFilter($storeId)
    {
        $storeId = intVal($storeId);
        $this->getSelect()->where('stores="" OR stores LIKE "%,'.$storeId.',%"');
        
        return $this;
    }    
    
    public function addCustomerGroupFilter($groupId)
    {
        $groupId = intVal($groupId);
        $this->getSelect()->where('cust_groups="" OR cust_groups LIKE "%,'.$groupId.',%"');
        
        return $this;
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('method_id','comment');
    }
}