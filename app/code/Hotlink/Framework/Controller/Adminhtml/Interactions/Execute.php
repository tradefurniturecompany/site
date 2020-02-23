<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions;

class Execute extends \Hotlink\Framework\Controller\Adminhtml\Interactions\Execute\AbstractExecute
{

    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hotlink\Framework\Model\Platform $platform,
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
        return 'Hotlink_Framework::interactions';
    }

    function getActiveMenuResource()
    {
        return 'Hotlink_Framework::interactions';
    }

}
