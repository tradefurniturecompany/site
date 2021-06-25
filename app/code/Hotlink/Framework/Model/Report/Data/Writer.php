<?php
namespace Hotlink\Framework\Model\Report\Data;

class Writer extends \Hotlink\Framework\Model\Report\Writer\AbstractWriter
{

	function getCode()
	{
		return 'data';
	}

	protected function _write( \Hotlink\Framework\Model\Report\Item $item )
	{
		if ( $data = $item->getData() )
			{
				if ( $reportId = $this->_getReportObject()->getId() )  // ReportId may not be set, in which case cannot write
					{
						$file = $this->report()->getFilePathItem( $reportId, $item->getId() );
						if ( $file )
							{
								$handle = fopen( $file, "w" );
								fwrite( $handle, serialize( $data ) );
								fclose( $handle );
							}
					}
			}
		return $this;
	}

	protected function _close()
	{
		return $this;
	}

}
