<?php
namespace Hotlink\Framework\Helper;

class Module
{

    protected $moduleList;
    protected $reflectionHelper;

    public function __construct( \Magento\Framework\Module\ModuleListInterface $moduleList,
                                 \Hotlink\Framework\Helper\Reflection $reflectionHelper
    )
    {
        $this->moduleList = $moduleList;
        $this->reflectionHelper = $reflectionHelper;
    }

    public function getName( $object )
    {
        return $this->reflectionHelper->getModule( $object );
    }

    public function getVersion( $moduleName )
    {
        $module = $this->moduleList->getOne( $moduleName );
        if ( $module )
            {
                return $module[ 'setup_version' ];
            }
        return false;
    }

    public function getDBVersion( $moduleName )
    {
        return \Magento\Framework\Module\ResourceInterface::getDbVersion( $moduleName );
    }

    public function isEnabled( $moduleName )
    {
        return $this->moduleList->has( $moduleName );
    }

}
