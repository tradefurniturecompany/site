<?php
namespace Hotlink\Framework\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractXdebugCommand extends Command
{

    protected $vendorpath = null;

    public function getVendorFolder()
    {
        if ( is_null( $this->vendorpath ) )
            {
                $this->vendorpath = require BP . '/app/etc/vendor_path.php';
            }
        return $this->vendorpath;
    }

    public function getPhpunitFile()
    {
        return realpath( BP . '/' . $this->getVendorFolder() . '/phpunit/phpunit/phpunit' );
    }

    // NB: this may be in a vendor folder like ..../vendor/module-home-folder/tests
    public function getTestsFolder( $from, $subfolder = false )
    {
        $path = ( $subfolder )
              ? \Hotlink\Framework\Filesystem::getRelativePath( $from, [ 'Test', $subfolder ] )
              : \Hotlink\Framework\Filesystem::getRelativePath( $from, [ 'Test' ] );
        return is_dir( $path ) ? $path : false;
    }

    public function makeBashDebugCommand( $command )
    {
        return 'export XDEBUG_CONFIG="idekey=session_name";' . $command;
    }

}
