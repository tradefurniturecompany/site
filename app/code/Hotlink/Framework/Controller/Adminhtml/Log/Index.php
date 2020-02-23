<?php
namespace Hotlink\Framework\Controller\Adminhtml\Log;

class Index extends \Hotlink\Framework\Controller\Adminhtml\Log\AbstractLog
{

    function execute()
    {
        $this
            ->_initPageLayout()
            ->_setActiveMenu( self::ACTIVE_MENU )
            ->_addBreadcrumb( 'Interaction Log', 'Interaction Log' )
            ->_setTitle( 'Interaction Log' );

        return $this->getPage();
    }

}
