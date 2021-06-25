<?php
namespace Hotlink\Framework\Model\Config\Field\Identifier;

class Source
{

    const ID_INCREMENT = 'increment_id';
    const ID_ENTITY    = 'entity_id';

    public function toOptionArray()
    {
        return array(
                     array('value' => self::ID_INCREMENT,
                           'label' => 'Increment ID' ),
                     array('value' => self::ID_ENTITY,
                           'label' => 'Entity ID' )
                     );
    }

    public function getOptions()
    {
        return array(
                     'Increment ID'  => self::ID_INCREMENT,
                     'Entity Id'     => self::ID_ENTITY
                     );
    }

}
