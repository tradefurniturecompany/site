<?php
namespace Hotlink\Framework\Model\Config\Field\Indexing\Before;

class Source
{

    const SYSTEM = 0;
    const MANUAL = 1;
    const REALTIME = 2;

    function toOptionArray()
    {
        return [
            [ 'label' => __( 'Use system settings' ), 'value' => self::SYSTEM ],
            [ 'label' => __( 'Disable' ),             'value' => self::MANUAL ],
            [ 'label' => __( 'Enable' ),              'value' => self::REALTIME ] ];
    }

}