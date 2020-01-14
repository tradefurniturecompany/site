<?php
namespace Hotlink\Brightpearl\Model\Stock;

class Item extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init( 'Hotlink\Brightpearl\Model\ResourceModel\Stock\Item', 'id' );
    }

    public function save()
    {
        if ( !$this->getItemId() ) {
            if ( ($item = $this->getMagentoStockItem()) || ($item = $this->getStockItem()) ) {
                if ($id = $item->getItemId()) {
                    $this->setItemId($id);
                }
                else {
                    return false;
                }
            }
        }
        $this->setTimestamp(time());
        parent::save();
        return true;
    }

}
