<?php
namespace Hotlink\Brightpearl\Workaround\Magento222\Magento\Catalog\Model\Product;

class Plugin
{

    const KEY = '_hotlink_brightpearl_m222_ScopeOverriddenValue_wrokaround';

    function beforeAddAttributeUpdate( $subject, $code, $value, $store )
    {
        if ( $data = $subject->getData( self::KEY ) )
            {
                if ( is_null( $value ) )
                    {
                        foreach ( $data as $update )
                            {
                                $key = isset( $update[ 'code' ] ) ? $update[ 'code' ] : false;
                                if ( $key == $code )
                                    {
                                        $val = array_key_exists( 'value', $update ) ? $update[ 'value' ] : null;
                                        if ( $value != $val )
                                            {
                                                $value = $val;
                                            }
                                        break;
                                    }
                            }
                    }
            }
        return array( $code, $value, $store );
    }

    /*

      Why this class exists...

      PROBLEM
      =======

      Product price attributes fail to save in Magento 2.2.2. when the first loaded product does not yet have
      the desired price attribute.

      SOLUTION
      ========

      Reverse the broken behaviour before it can be commited to disk.
      
      EXPLANATION
      ===========

      A. The first part of the save process in Magento2 handles Eav attributes and scopes,
      and correctly saves all the designated attributes.

      This inserts all new attributes required - for example if the Admin Scope has a 'cost'
      attribute, but the the "Main Website" does not, a price update in "Main Website" will insert
      a new attribute record for each store under Main Website, holding the new price.

      Before commiting this correct good behaviour,

         \Magento\Framework\EntityManager\Operation\Update::execute($entity, $arguments = [])

         fires event 'magento_catalog_api_data_productinterface_save_after'

      B. The event is handled in 

           Magento\Framework\EntityManager\Observer\AfterEntitySave::execute(Observer $observer)

        If the passed object is an instance of Magento\Framework\Model\AbstractModel, then (in order)

        1. $entity->getResource()->loadAllAttributes($entity);

            This loads all product attributes, which although inefficient may be required for updating product
            rules or other indexes. This introduces catalogue url duplication problems which must be worked around.

        2. $entity->getResource()->afterSave($entity);

           This causes all attribute backend models to be walked, and applies afterSave to these.

           In particular (for the price attributes like 'cost'), this function is called:

               \Magento\Catalog\Model\Product\Attribute\Backend\Price::afterSave($object)
 
           This checks where the attribute already exists in the scope being saved, and if it doesn't (which is
           guaranteed in this scenario), then it assigns the attribute value to null, and deletes the price
           attribute for each store in which it was correctly inserted by (A).

           The premise is that if the attribute does not already exist in the scope being saved, then it
           should be deleted. Since our intention is to create the attribute in the current scope, this
           makes standard product save unusable for adding prices to different website.


      TEST
      ====

      $broken = \Magento\Catalog\Model\Attribute\ScopeOverriddenValue

      This class is funadamentally broken, to the extent that it's function signatures don't work.

      The test sequence below will return incorrect results, which due to the low level importance of the class,
      introduce dangerous bugs particularly wrt. attribute scoping, but also across different entity types.

      test requirements:
          two different entity types            $entityType1, $entityType2
          three different entities              $entity1, $entity2, $entity3
          object instance (which is shared)     $broken

      start with this:

      1. $broken->getDefaultValues( $entityType1, $entity1 )

          OK : as this is the first call, it populates available attributes of $entityType1 / $entity1 and returns

      2. $broken->getDefaultValues( $entityType1, $entity2 )

          FAIL : returns the attributes of $entity1 not $entity2

          $entity2 may contain attributes $entity1 does not, and vice versa
          manifests as a product failing to update a price, eg 'cost', when the first product encountered
          does not have an existing cost attribute

      3. $broken->getDefaultValues( $entityType2, $entity3 )

          FAIL : returns nothing

          initialisation check for scope loaded attributes is naieve and does not consider types / entities

      DECISION
      ========

      The fix will require Magento to change function (and possibly class) signatures to decouple inappropriate
      binding of attribute/backend with ScopeOverriddenValue.

      That makes this section of Magento too unstable to work with (any overload or plugins are likely to break
      in future), so we simply bypass it entirely using more stable infrastructure (Product).

     */

}
