<?php
namespace Hotlink\Framework\Model\Report\Item;

class Writer extends \Hotlink\Framework\Model\Report\Writer\AbstractWriter
{

    protected $_file = false;
    protected $_handle = false;
    protected $_reportId = false;

    /**
     * @var \Hotlink\Framework\Helper\Report
     */
    protected $interactionReportHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Report $interactionReportHelper
    ) {
        $this->interactionReportHelper = $interactionReportHelper;
    }
    public function getCode()
    {
        return 'detail';
    }

    protected function _open( \Hotlink\Framework\Model\Report $report )
    {
        parent::_open( $report );
        if ( $reportId = $this->_getReportObject()->getId() )  // ReportId may not be set, in which cannot write data
            {
                $this->_reportId = $reportId;
                $this->_file = $this->interactionReportHelper->getFilePath( $reportId );
                if ( $this->_file )
                    {
                        $this->_handle = fopen( $this->_file, "w" );
                    }
            }
    }

    protected function _write( \Hotlink\Framework\Model\Report\Item $item )
    {
        if ( $this->_reportId && $this->_handle )
            {
                $data = serialize( $item );
                fwrite( $this->_handle, $data . "\n" );
            }
        return $this;
    }

    protected function _close()
    {
        if ( $this->_handle )
            {
                fclose( $this->_handle );
                $this->_handle = false;
            }
        return $this;
    }

}