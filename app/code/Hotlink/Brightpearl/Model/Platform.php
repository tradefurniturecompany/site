<?php
namespace Hotlink\Brightpearl\Model;

class Platform extends \Hotlink\Framework\Model\Platform\AbstractPlatform
{

    const APP_REF_M1 = 'bpmagento';
    const APP_REF_M2 = 'bpmagento2';
    const DEV_REF = 'brightpearl';

    function getCode()
    {
        return 'brightpearl';
    }

    protected function _getName()
    {
        return "Brightpearl";
    }

    function getModulePath()
    {
        return \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [], 1 );
    }

    function getSection()
    {
        return 'hotlink_brightpearl';
    }

    function getGroup()
    {
        return 'authorisation';
    }

    function getField()
    {
        return 'accountCode';
    }

}