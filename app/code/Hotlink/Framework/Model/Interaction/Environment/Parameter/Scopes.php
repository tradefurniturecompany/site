<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter;

class Scopes extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    /**
     * @var \Hotlink\Framework\Helper\Html\Form\Environment\Parameter\Scopes
     */
    protected $scopesHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Html\Form\Environment\Parameter\Scopes $scopesHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->scopesHelper = $scopesHelper;
        $this->storeManager = $storeManager;

        parent::__construct(
            $exceptionHelper,
            $parameterHelper,
            $data );
    }

    public function getDefault()
    {
        return \Magento\Store\Model\Store::DEFAULT_CODE;
    }

    public function getName()
    {
        return 'Scopes';
    }

    public function getKey()
    {
        return 'scopes';
    }

    public function getFormHelper()
    {
        return $this->scopesHelper;
    }

    public function getSize()
    {
        $stores = $this->storeManager->getStores();
        $groups = $this->storeManager->getGroups();
        $websites = $this->storeManager->getWebsites();
        $count = count( $stores ) + count( $groups ) + count( $websites );
        return $count + 1;
    }

    public function getMultiselect()
    {
        return true;
    }

    public function getAdminVisible()
    {
        return true;
    }

    public function getWebsitesVisible()
    {
        return true;
    }

    public function getGroupsVisible()
    {
        return true;
    }

    public function getStoresVisible()
    {
        return true;
    }

    public function getAdminEnabled()
    {
        return true;
    }

    public function getWebsitesEnabled()
    {
        return true;
    }

    public function getGroupsEnabled()
    {
        return true;
    }

    public function getStoresEnabled()
    {
        return true;
    }

    public function asString()
    {
        $output = "";

        $value = $this->getValue();

        if ( !is_array( $value ) )
            {
                $value = array( $value );
            }

        $counter = 0;
        foreach ( $value as $scope => $stores )
            {
                $counter++;
                if ( $counter > 1 )
                    {
                        $output .= ", ";
                    }

                $scopeText = '';
                $counter2 = 0;
                foreach ( $stores as $store => $storeId )
                    {
                        $counter2++;
                        if ( $counter2 > 1 )
                            {
                                $scopeText .= ",";
                            }
                        if ( $label = $this->_getLabel( $store ) )
                            {
                                $scopeText .= " ($label)";
                            }
                        else
                            {
                                $scopeText .= ( string ) $store;
                            }
                    }
                $output .= $scope . '{' . $scopeText . '}';
            }

        if ( ( count( $value ) > 1 ) || $this->getMultiSelect() )
            {
                $output = "[ " . $output . " ]";
            }

        $output = $this->getName() . ' = ' . $output;
        return $output;
    }

}
