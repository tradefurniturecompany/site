<?php
namespace Hotlink\Framework\Model;

class Platform extends \Hotlink\Framework\Model\Platform\AbstractPlatform
{

    function getCode()
    {
        return 'hotlink';
    }

    protected function _getName()
    {
        return 'Hotlink Framework';
    }

    function getModulePath()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [], 1 );
    }

    function getSection()
    {
        return 'hotlink_framework';
    }

    function getGroup()
    {
        return 'installation';
    }

    function getField()
    {
        return 'id';
    }

}
