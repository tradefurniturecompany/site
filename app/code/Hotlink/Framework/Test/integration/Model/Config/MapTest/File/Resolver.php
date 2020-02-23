<?php
namespace Hotlink\Framework\Test\integration\Model\Config\MapTest\File;

class Resolver implements \Magento\Framework\Config\FileResolverInterface
{

    protected $reader;
    protected $_root;

    function __construct( \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
                                 $root )
    {
        $this->reader = $readFactory->create( $root );
        $this->_root = $root;
    }
    
    function get( $filename, $scope )
    {
        $files = $this->reader->search( "{*$filename,*/*$filename,*/*/*$filename,*/*/*/*$filename}" );
        $results = [];
        foreach ( $files as $file )
            {
                $filepath = $this->reader->getAbsolutePath( $file );
                $content = @file_get_contents( $filepath );
                $results[ $filepath ] = $content;
            }
        return $results;
    }

}
