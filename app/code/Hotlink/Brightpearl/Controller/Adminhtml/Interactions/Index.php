<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Interactions;

class Index extends \Hotlink\Framework\Controller\Adminhtml\Interactions\Index\AbstractIndex
{

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Brightpearl\Model\Platform $platform,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct(
            $context,
            $platform,
            $registry
        );
    }

    function getActiveMenuId()
    {
        return 'Hotlink_Brightpearl::interactions';
    }

    function getActiveMenuResource()
    {
        return 'Hotlink_Brightpearl::interactions';
    }

}
