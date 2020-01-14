<?php
namespace Hotlink\Framework\Model\Report;

class Reader extends \Hotlink\Framework\Model\Stream\Reader
{

    protected $_reportObject = false;    // do not use $_report as it's already used to support the IReport interface
    protected $_itemFile = false;
    protected $_itemFileHandle = false;

    protected function _open( \Hotlink\Framework\Model\Report $report )
    {
        $this->_reportObject = $report;

        if ( $this->_openLogRecord( $report ) )
            {
                $this->_openItemFile( $report );
                return true;
            }
        return false;
    }

    protected function _openLogRecord( \Hotlink\Framework\Model\Report $report )
    {
        $log = $this->report()->logFactory()->create();
        $log->load( $report->getId() );
        if ( $log->getId() )
            {
                $report->setUser(    $log->getUser() );
                $report->setTrigger( $log->getTrigger() );
                $report->setContext( $log->getContext() );
                $report->setEvent(   $log->getEvent() );
                $report->setProcess( $log->getInteraction() );
                return true;
            }
        return false;
    }

    protected function _openItemFile( \Hotlink\Framework\Model\Report $report )
    {
        $this->_itemFile = $this->report()->getFilePath( $report->getId(), false );
        if ( $this->_itemFile )
            {
                if ( file_exists( $this->_itemFile ) && is_readable( $this->_itemFile ) )
                    {
                        $this->_itemFileHandle = fopen( $this->_itemFile, "r" );
                        return true;
                    }
            }
        return false;
    }

    protected function _read()
    {
        return $this->_readItem();
    }

    protected function _readItem()
    {
        $item = false;
        if ( $this->_itemFileHandle )
            {
                if ( ! feof( $this->_itemFileHandle ) )
                    {
                        $line = fgets( $this->_itemFileHandle );
                        if ( $line )
                            {
                                $item = unserialize( $line );
                                $report = $this->_getReportObject();
                                if ( $report->getUseDataReader() )
                                    {
                                        if ( $data = $this->_readItemData( $report->getId(), $item ) )
                                            {
                                                $item->setData( $data );
                                            }
                                    }
                            }
                    }
            }
        return $item;
    }

    protected function _readItemData( $reportId, \Hotlink\Framework\Model\Report\Item $item )
    {
        $file = $this->report()->getFilePathItem( $reportId, $item->getId() );
        if ( $file )
            {
                if ( file_exists( $file ) && is_readable( $file ) )
                    {
                        $data = file_get_contents( $file );
                        $data = unserialize( $data );
                        return $data;
                    }
            }
    }

    protected function _close()
    {
    }

    protected function _getReportObject()
    {
        return $this->_reportObject;
    }

    protected function _getExceptionHelper()
    {
        return $this->interactionExceptionHelper;
    }

}