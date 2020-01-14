<?php
namespace Hotlink\Brightpearl\Model;

class Platform extends \Hotlink\Framework\Model\Platform\AbstractPlatform
{

    const APP_REF_M1 = 'bpmagento';
    const APP_REF_M2 = 'bpmagento2';
    const DEV_REF = 'brightpearl';

    public function getCode()
    {
        return 'brightpearl';
    }

    protected function _getName()
    {
        return "Brightpearl";
    }

    public function getModulePath()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [], 1 );
    }

    public function getSection()
    {
        return 'hotlink_brightpearl';
    }

    public function getGroup()
    {
        return 'authorisation';
    }

    public function getField()
    {
        return 'accountCode';
    }

}