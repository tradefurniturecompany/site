<?php
namespace Hotlink\Framework\Model;

class Platform extends \Hotlink\Framework\Model\Platform\AbstractPlatform
{

    public function getCode()
    {
        return 'hotlink';
    }

    protected function _getName()
    {
        return 'Hotlink Framework';
    }

    public function getModulePath()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [], 1 );
    }

    public function getSection()
    {
        return 'hotlink_framework';
    }

    public function getGroup()
    {
        return 'installation';
    }

    public function getField()
    {
        return 'id';
    }

}
