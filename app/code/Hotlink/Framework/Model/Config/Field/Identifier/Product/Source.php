<?php
namespace Hotlink\Framework\Model\Config\Field\Identifier\Product;

class Source
{

    const ID_SKU       = 'SKU';
    const ID_ENTITY    = 'Entity';

    public function toOptionArray()
    {
        return array(
                     array('value' => self::ID_SKU,
                           'label' => 'Product SKU' ),
                     array('value' => self::ID_ENTITY,
                           'label' => 'Product ID' )
                     );
    }

    public function getOptions()
    {
        return array(
                     'Product SKU'  => self::ID_SKU,
                     'Product ID'     => self::ID_ENTITY
                     );
    }
    
}
