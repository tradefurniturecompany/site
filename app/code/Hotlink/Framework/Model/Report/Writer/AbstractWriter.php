<?php
namespace Hotlink\Framework\Model\Report\Writer;

abstract class AbstractWriter extends \Hotlink\Framework\Model\Stream\Writer
{

    protected $_reportObject = false;    // do not use $_report as it's already used to support the IReport interface

    abstract protected function _write( \Hotlink\Framework\Model\Report\Item $item );

    protected function _open( \Hotlink\Framework\Model\Report $report )
    {
        $this->_reportObject = $report;
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
