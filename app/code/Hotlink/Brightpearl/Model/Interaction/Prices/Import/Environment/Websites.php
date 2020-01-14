<?php
namespace Hotlink\Brightpearl\Model\Interaction\Prices\Import\Environment;

class Websites extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Scopes
{

    public function getDefault()
    {
        $admin = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE );

        $websites = [];
        foreach ( $this->storeManager->getWebsites( false, true ) as $code => $website )
            {
                $websites[$code] = $website->getId();
            }

        $result = [];
        $result[ \Magento\Store\Model\Store::ADMIN_CODE ] = [ $admin->getCode() => $admin->getStoreId() ];
        if ( count( $websites ) > 0 )
            {
                $result[ 'website' ] = $websites;
            }
        return $result;
    }

    public function getKey()
    {
        return 'websites';
    }

    public function getName()
    {
        return 'Website(s)';
    }

    public function getNote()
    {
        return '';
    }

    public function getGroupsVisible()
    {
        return false;
    }

    public function getStoresVisible()
    {
        return false;
    }

}
