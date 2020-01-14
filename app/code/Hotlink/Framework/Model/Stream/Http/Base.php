<?php
namespace Hotlink\Framework\Model\Stream\Http;

abstract class Base extends \Hotlink\Framework\Model\Stream\Writer
{

    protected $_gzipHeaderChecked = false;
    protected $_buffer_size;
    protected $_buffer_increment;

    protected $config;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Model\Config\Module\Installation $config
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->config = $config;
    }

    protected function _writeHeader( $header = "", $buffer_increment = 100 )
    {
        $headers = headers_list();
        $result = true;
        if ( ! headers_sent() )
            {
                header( $header );
                $this->_autoSendGzipHeader();
                $result = false;
            }
        $this->_buffer_increment = $buffer_increment;
        $this->_buffer_size = $this->_calculateBufferSize( $buffer_increment );
        return $result;
    }

    protected function _autoSendGzipHeader()
    {
        if ( !$this->_gzipHeaderChecked )
            {
                $handlers = ob_list_handlers();
                if ( $this->config->getWriteStreamingGzipHeader( null ) )
                    {
                        if ( in_array( 'zlib output compression', $handlers ) )
                            {
                                header('Content-Encoding: gzip');
                            }
                    }
                $this->_gzipHeaderChecked = true;
            }
    }

    protected function _calculateBufferSize( $initialSize )
    {
        $size = $initialSize;
        $status = ob_get_status( true );

        if ( count( $status ) > 0 )
            {
                if ( array_key_exists( 'buffer_size', $status[ 0 ] ) )
                    {
                        $size = $size + $status[ 0 ][ 'buffer_size' ];
                    }
                if ( array_key_exists( 'chunk_size', $status[ 0 ] ) )
                    {
                        $size = $size + $status[ 0 ][ 'chunk_size' ];
                    }
            }
        return $size;
    }

}
