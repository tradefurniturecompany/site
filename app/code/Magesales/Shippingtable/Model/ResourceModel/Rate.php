<?php
namespace Magesales\Shippingtable\Model\ResourceModel;
class Rate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {    
        $this->_init('shippingrate', 'rate_id');
    }

    public function batchInsert($methodId, $data)
    {
        $err = '';
       
        $sql = '';
        for ($i=0, $n=count($data); $i<$n; ++$i){
            $sql .= ' (NULL,' . $methodId;
            foreach ($data[$i] as $v){
                $sql .= ', "'.$v.'"';
            }
            $sql .= '),';
        } 
        
        if ($sql)
		{
			$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
			$connection= $this->_resources->getConnection();

			$themeTable = $this->_resources->getTableName('shippingrate');

            $sql = 'INSERT INTO `' . $themeTable . '` VALUES ' . substr($sql, 0, -1);
            try {
				$connection->query($sql);
            } 
            catch (\Exception $e) {
                $err = $e->getMessage();
            }
        }
            
        return $err;
    } 
    
    public function deleteBy($methodId)
    {
		$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
		$connection = $this->_resources->getConnection();

		$themeTable = $this->_resources->getTableName('shippingrate');
		
        $connection->delete($themeTable, 'method_id=' . intVal($methodId)); 
    }     
       
}