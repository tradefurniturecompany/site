<?php
namespace Hotlink\Brightpearl\Model\Platform;

class Data extends \Hotlink\Framework\Model\Platform\Data
{

    public function getChildClassDefault( $key )
    {
        return '\Hotlink\Brightpearl\Model\Platform\Data';
    }

    public function getMappings()
    {
        return \Hotlink\Brightpearl\Model\Platform\Type::getMappings();
    }

    public function toArray(array $arrAttributes = array())
    {
        $ret = array();
        foreach ( $this as $key => $val )
            {
                if ( $val instanceof \Hotlink\Brightpearl\Model\Platform\Data )
                    {
                        $ret[$key] = $val->toArray();
                    }
                else
                    {
                        $ret[$key] = $val;
                    }
            }
        return $ret;
    }

}
