<?php
namespace Hotlink\Framework\Model\Config\Module;

class Installation extends \Hotlink\Framework\Model\Config\Module\AbstractConfig
{

    protected function _getGroup()
    {
        return 'installation';
    }

    function getLogPath( $storeId )
    {
        return $this->getConfigData( 'interaction_report_log_path', $storeId );
    }

    function getWriteStreamingGzipHeader( $storeId )
    {
        return $this->getConfigData( 'write_streaming_gzip_header', $storeId );
    }

}
