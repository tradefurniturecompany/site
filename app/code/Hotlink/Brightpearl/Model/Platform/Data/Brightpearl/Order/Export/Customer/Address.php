<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Customer;

class Address extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Address $address )
    {
        $this[ 'company'      ] = $address->getCompany();
        $this[ 'firstName'    ] = $address->getFirstname();
        $this[ 'lastName'     ] = $address->getLastname();
        $this[ 'name'         ] = trim( $address->getFirstname() . ' ' . $address->getLastname() );
        $this[ 'addressLine1' ] = trim( $address->getStreetLine( 1 ) );
        $this[ 'addressLine2' ] = trim( $address->getStreetLine( 2 ) );
        $this[ 'addressLine3' ] = trim( $address->getCity() );
        $this[ 'addressLine4' ] = trim( $address->getStreetLine( 3 ) . ' ' . $address->getStreetLine( 4 ) . ' ' . $address->getRegion() );
        $this[ 'postalCode'   ] = trim( $address->getPostcode() );
        $this[ 'countryCode'  ] = trim( $address->getCountryId() );
        $this[ 'telephone'    ] = trim( $address->getTelephone() );
    }

}