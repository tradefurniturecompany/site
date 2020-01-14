<?php
namespace Magesales\Shippingtable\Controller\Adminhtml\Method;
use Magento\Framework\Controller\ResultFactory;
use Magesales\Shippingtable\Controller\Adminhtml\Method;

class MassActivate extends Method
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->_modifyStatus(1);
    }
}
