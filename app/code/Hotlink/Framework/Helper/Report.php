<?php
namespace Hotlink\Framework\Helper;

class Report
{

    protected $pathHelper;
    protected $installationConfig;
    protected $reportFactory;
    protected $reportLogFactory;
    protected $reportLogWriterFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Path $pathHelper,
        \Hotlink\Framework\Model\Config\Module\Installation $installationConfig,
        \Hotlink\Framework\Model\Report\LogFactory $reportLogFactory,
        \Hotlink\Framework\Model\Report\Log\WriterFactory $reportLogWriterFactory,
        \Hotlink\Framework\Model\ReportFactory $reportFactory
    )
    {
        $this->pathHelper = $pathHelper;
        $this->installationConfig = $installationConfig;
        $this->reportLogFactory = $reportLogFactory;
        $this->reportFactory = $reportFactory;
        $this->reportLogWriterFactory = $reportLogWriterFactory;
    }

    public function create( \Hotlink\Framework\Model\Report\IReport $object )
    {
        return $this->reportFactory->create( [ 'object' => $object ] );
    }

    public function delete( $log )
    {
        if ( !is_object( $log ) )
            {
                $log = $this->logFactory()->create()->load( $log );
            }

        // Remove item file
        $itemFile = $this->getFilePath( $log->getId(), false );
        if ( file_exists( $itemFile ) )
            {
                unlink( $itemFile );
            }

        if ( $itemFile )
            {
                // Remove item data files
                $itemDataFiles = glob( $itemFile . ".*" );
                foreach ( $itemDataFiles as $itemDataFile )
                    {
                        if ( is_file( $itemDataFile ) && file_exists( $itemDataFile ) )
                            {
                                unlink( $itemDataFile );
                            }
                    }
            }
        $log->delete();
        return true;
    }

    public function logFactory()
    {
        return $this->reportLogFactory;
    }

    public function logWriterFactory()
    {
        return $this->reportLogWriterFactory;
    }

    public function createLogWriter()
    {
        return $this->reportLogWriterFactory->create();
    }

    public function getFilePath( $id, $autoCreatePath = true, $storeId = null )
    {
        $file = false;
        if ( $path = $this->getPath( $autoCreatePath, $storeId ) )
            {
                $helper = $this->pathHelper;
                if ( $name = $this->getFilename( $id ) )
                    {
                        $file = $helper->join( $path, $name );
                        if ( $autoCreatePath )
                            {
                                $folder = dirname( $file );
                                $helper->create( $folder );
                            }
                    }
            }
        return $file;
    }

    public function getFilePathItem( $reportId, $itemId, $autoCreatePath = true, $storeId = null )
    {
        $file = $this->getFilePath( $reportId, $autoCreatePath, $storeId );
        if ( $file )
            {
                $file .= '.' . $itemId;
            }
        return $file;
    }

    public function getPath( $autoCreatePath, $storeId = null )
    {
        $path = $this->_getConfig()->getLogPath( $storeId );
        if ( $path )
            {
                $helper = $this->pathHelper;
                $path = $helper->absolute( $path );
                if (!$helper->exists( $path ) && $autoCreatePath)
                    {
                        $helper->create( $path );
                    }

                if ($helper->isWriteable( $path ))
                    {
                        return $path;
                    }
            }
        return false;
    }

    public function getFilename( $id )
    {
        $ret = false;
        if ( $id )
            {
                $ret = str_pad( $id, 9, '0', STR_PAD_LEFT ).'.log';
                $l1 = substr( $ret, 0, 3 );
                $l2 = substr( $ret, 3, 3 );
                $ret = $l1 . DIRECTORY_SEPARATOR . $l2 . DIRECTORY_SEPARATOR . $ret;
            }
        return $ret;
    }

    protected function _getConfig()
    {
        return $this->installationConfig;
    }

}
