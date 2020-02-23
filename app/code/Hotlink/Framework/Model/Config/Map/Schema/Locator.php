<?php
namespace Hotlink\Framework\Model\Config\Map\Schema;

class Locator implements \Magento\Framework\Config\SchemaLocatorInterface
{

    function getSchema()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [ 'etc', 'hotlink.xsd' ], 4 );
    }

    function getPerFileSchema()
    {
        return null;
    }

}
