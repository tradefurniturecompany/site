<?php
namespace Hotlink\Framework\Controller\Adminhtml\Interactions;

class Index extends \Hotlink\Framework\Controller\Adminhtml\Interactions\Index\AbstractIndex
{

    public function __construct(
        \Magento\Backend\App\Action\Context $context,

        \Hotlink\Framework\Model\Platform $platform,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct(
            $context,
            $platform,
            $registry
        );
    }

    public function getActiveMenuId()
    {
        return 'Hotlink_Framework::interactions';
    }

    public function getActiveMenuResource()
    {
        return 'Hotlink_Framework::interactions';
    }

}
