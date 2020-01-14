<?php
namespace Magesales\Shippingtable\Model\ResourceModel;
class Method extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {    
        $this->_init('shippingmethod', 'method_id');
    }          
}