<?php
namespace Hotlink\Framework\Controller\Adminhtml\Log;

class View extends \Hotlink\Framework\Controller\Adminhtml\Log\AbstractLog
{

    public function execute()
    {
        $logId = ( int ) $this->getRequest()->getParam( 'id' );
        if ( $logId )
            {
                $this->registry->register( 'hotlink_framework_report_log_id', $logId );
                $this
                    ->_initPageLayout()
                    ->_setActiveMenu( self::ACTIVE_MENU )
                    ->_addBreadcrumb( 'Log Label', 'Log Title' )
                    ->_setTitle( 'Interaction Log Report #' . $logId );

                return $this->getPage();
            }
        return $this->_redirect( '/*/*/*' );
    }

}
