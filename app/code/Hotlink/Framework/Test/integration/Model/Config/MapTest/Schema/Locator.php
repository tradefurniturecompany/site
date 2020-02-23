<?php
namespace Hotlink\Framework\Test\integration\Model\Config\MapTest\Schema;

class Locator extends \Hotlink\Framework\Model\Config\Map\Schema\Locator
{

    function getSchema()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [ 'etc', 'hotlink.xsd' ], 6 );
    }

}
