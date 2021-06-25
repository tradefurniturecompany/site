<?php
namespace Hotlink\Framework\Helper\Convention\Interaction;

class Config extends \Hotlink\Framework\Helper\Convention\AbstractConvention
{

    protected $groupKeyHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Reflection $reflection,
        \Hotlink\Framework\Helper\Convention\Check $check,
        \Hotlink\Framework\Helper\Config\Interaction\Group\Key $groupKeyHelper
    )
    {
        $this->groupKeyHelper = $groupKeyHelper;
        parent::__construct( $reflection, $check );
    }

    public function getSectionKey( $thing )
    {
        $module = $this->reflection->getModule( $thing );
        $key = strtolower( $module );
        return $key;
    }

    public function getGroupKey( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        return $this->groupKeyHelper->encode( $interaction );
    }

    public function getGroupKeyForConfig( \Hotlink\Framework\Model\Interaction\Config\AbstractConfig $config )
    {
        $class = $this->reflection->getClass( $config, null, false );
        $parts = explode( '\\', $class );
        array_pop( $parts );
        $class = implode( '\\', $parts );
        return $this->getGroupKeyForInteraction( $class );
    }

    public function getGroupKeyForInteraction( $interaction )
    {
        return $this->groupKeyHelper->encode( $interaction );
    }

}
