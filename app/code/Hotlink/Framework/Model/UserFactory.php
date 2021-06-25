<?php
namespace Hotlink\Framework\Model;

class UserFactory
{

    protected $scopeHelper;
    protected $factory;

    function __construct(
        \Hotlink\Framework\Helper\Scope $scopeHelper,
        \Hotlink\Framework\Helper\Factory $factory
    )
    {
        $this->scopeHelper = $scopeHelper;
        $this->factory = $factory;
    }

    function create()
    {
        $sapi = php_sapi_name();
        switch ( $sapi )
            {
                case 'cli':
                    return $this->factory->create( \Hotlink\Framework\Model\User\Console::class );
                default:
                    if ( $this->scopeHelper->isAdmin() )
                        {
                            return $this->factory->create( \Hotlink\Framework\Model\User\Admin::class );
                        }
                    else
                        {
                            return $this->factory->create( \Hotlink\Framework\Model\User\Frontend::class );
                        }
                    break;
            }
    }

}
