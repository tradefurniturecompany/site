<?php
namespace Hotlink\Framework\Helper;

class Module
{

    protected $moduleList;
    protected $reflectionHelper;

    function __construct( \Magento\Framework\Module\ModuleListInterface $moduleList,
                                 \Hotlink\Framework\Helper\Reflection $reflectionHelper
    )
    {
        $this->moduleList = $moduleList;
        $this->reflectionHelper = $reflectionHelper;
    }

    function getName( $object )
    {
        return $this->reflectionHelper->getModule( $object );
    }

    function getVersion( $moduleName )
    {
        $module = $this->moduleList->getOne( $moduleName );
        if ( $module )
            {
                return $module[ 'setup_version' ];
            }
        return false;
    }

    function getDBVersion( $moduleName )
    {
        return \Magento\Framework\Module\ResourceInterface::getDbVersion( $moduleName );
    }

}
