<?php
namespace Hotlink\Framework\Controller\Adminhtml\Log;

class Delete extends \Hotlink\Framework\Controller\Adminhtml\Log\AbstractLog
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ( $id )
            {
                try
                    {
                        $log = $this->reportHelper->logFactory()->create()->load( $id );

                        if ( $log->getRecordId() )
                            {
                                $this->reportHelper->delete( $log );
                                $this->messageManager->addSuccess( __('Interaction Report Log #' . $id . ' successfully deleted!') );
                            }
                        else
                            {
                                $this->messageManager->addError( __('Interaction Report Log #' . $id . ' not found!') );
                            }
                    }
                catch ( \Exception $e )
                    {
                        $this->messageManager->addError( $e->getMessage() );
                    }
            }

        return $this->_redirect( '*/*/' );
    }

}
