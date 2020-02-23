<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Interactions;

class Execute extends \Hotlink\Framework\Controller\Adminhtml\Interactions\Execute\AbstractExecute
{

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Brightpearl\Model\Platform $platform,
        \Hotlink\Framework\Controller\Adminhtml\ResponseFactory $responseFactory
    )
    {
        parent::__construct(
            $context,
            $platform,
            $responseFactory
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
