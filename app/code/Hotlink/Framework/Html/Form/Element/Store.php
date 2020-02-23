<?php
namespace Hotlink\Framework\Html\Form\Element;

class Store extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    protected $storeManager;

    function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        $this->storeManager = $storeManager;

        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $data );
    }

    function getElementHtml()
    {
        $html = $this->getBeforeHtml();

        $name = $this->getName() . ( $this->getMultiselect() ?  '[]' : '' );

        $multiple = '';
        $size = '';
        if ( $this->getMultiselect() )
            {
                $multiple = ' multiple="multiple"';
                $size = ( $this->getSize() ) ? ' size="' . $this->getSize() .'"' : '';
            }
        $html .= '<select id="'. $this->getHtmlId() .'" name="'. $name .'"'. $multiple . $size . '>';
        foreach ( $this->getOptions() as $value => $option )
            {
                if ( $option['is_group'] )
                    {
                        if ( $option['is_close'] )
                            {
                                $html .= '</optgroup>';
                            }
                        else
                            {
                                $html .= '<optgroup label="' . $option['label'] . '" style="' .$option['style'] .'">';
                            }
                        continue;
                    }
                else
                    {
                        $html .= '<option value="'. $value .'"';
                        $html .= ( array_key_exists( 'url', $option ) ) ? ' url="' . $option['url'] .'"' : '';
                        if ( array_key_exists( 'selected', $option ) && $option[ 'selected' ] )
                            {
                                $html .= ' selected="selected"';
                            }
                        if ( array_key_exists( 'disabled', $option ) )
                            {
                                if ( $option[ 'disabled' ] )
                                    {
                                        $html .= ' disabled="disabled"';
                                    }
                            }
                        $html .= ( array_key_exists( 'style', $option ) ) ? ' style="' . $option['style'] . '">' : '';
                        $html .= $option['label'];
                        $html .= '</option>';
                    }
            }
        $html .= '</select>' . $this->getAfterHtml();
        return $html;
    }

    protected function getOptions()
    {
        $websitesEnabled = $this->getWebsitesEnabled();
        $websitesVisible = $this->getWebsitesVisible();

        $storesEnabled = $this->getStoresEnabled();
        $storesVisible = $this->getStoresVisible();

        //$storeModel = Mage::getSingleton( 'adminhtml/system_store' );

        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $options = array();
        $groups = array();

        $indent = 0;

        if ( $this->getAdminVisible() )
            {
                if ( $this->getAdminEnabled() )
                    {
                        $this->addOption( $options, 'default', 'Default', $this->isSelected( 'default' ) );
                    }
                else
                    {
                        $this->addOptionOpen( $options, 'default', 'Default' );
                    }
            }

        if ( $this->getWebsitesVisible() )
            {
                $this->addWebsites( $options, $indent + 1 );
            }
        else
            {
                if ( $this->getGroupsVisible() )
                    {
                        $this->addGroups( $options, $indent + 1 );
                    }
                else
                    {
                        $this->addStores( $options, $indent + 1 );
                    }
            }

        if ( $this->getAdminVisible() && !$this->getAdminEnabled() )
            {
                $this->addOptionClose( $options, 'default', 'Default' );
            }
        return $options;
    }

    protected function addWebsites( &$options, $indent )
    {
        // $storeModel = Mage::getSingleton( 'adminhtml/system_store' );

        foreach ( $this->storeManager->getWebsites() as $website )
            {
                $websiteId = $website->getWebsiteId();
                $name = $this->indent( $website->getName(), $indent );
                $code = 'website_' . $website->getCode();
                if ( $this->getWebsitesEnabled() )
                    {
                        $this->addOption( $options, $code, $name, $this->isSelected( $code ) );
                        $this->addGroups( $options, $indent + 1, $websiteId );
                    }
                else
                    {
                        $this->addOptionOpen( $options, $code, $name );
                        $this->addGroups( $options, $indent + 1, $websiteId );
                        $this->addOptionClose( $options, $code, $name );
                    }
            }
    }

    //
    //  Groups are actually badly named Stores within Magento code
    //
    protected function addGroups( &$options, $indent, $websiteId = false )
    {
        if ( !$this->getGroupsVisible() )
            {
                return;
            }
        // $storeModel = Mage::getSingleton( 'adminhtml/system_store' );
        foreach ( $this->storeManager->getGroups() as $group )
            {
                if ( ( $websiteId !== false ) && ( $group->getWebsiteId() != $websiteId ) )
                    {
                        continue;
                    }
                $groupId = $group->getId();
                $name = $this->indent( $group->getName(), $indent );
                $code = 'group_' . $groupId;

                if ( $this->getGroupsEnabled() )
                    {
                        $this->addOption( $options, $code, $name, $this->isSelected( $code ) );
                        $this->addStores( $options, $indent + 1, $groupId );
                    }
                else
                    {
                        $this->addOptionOpen( $options, $code, $name );
                        $this->addStores( $options, $indent + 1, $groupId );
                        $this->addOptionClose( $options, $code, $name );
                    }
            }
    }

    //
    //  Stores are actually badly named store views within Magento code
    //
    protected function addStores( &$options, $indent, $groupId = false )
    {
        if ( !$this->getStoresVisible() )
            {
                return;
            }
        //$storeModel = Mage::getSingleton( 'adminhtml/system_store' );
        foreach ( $this->storeManager->getStores() as $store )
            {
                if ( ( $groupId !== false ) && ( $store->getGroupId() != $groupId ) )
                    {
                        continue;
                    }
                $name = $this->indent( $store->getName(), $indent );
                $code = 'store_' . $store->getCode();
                if ( $this->getStoresEnabled() )
                    {
                        $this->addOption( $options, $code, $name, ( $this->getValue() == $code ) );
                    }
                else
                    {
                        $this->addOptionOpen( $options, $code, $name );
                        $this->addOptionClose( $options, $code, $name );
                    }
            }
    }

    protected function addOptionOpen( &$options, $code, $name )
    {
        $this->addOption( $options, $code.'_open', $name, false, true, false, 'background:#ccc; font-weight:bold;' );
    }

    protected function addOptionClose( &$options, $code, $name )
    {
        $this->addOption( $options, $code.'_close', $name, false, true, true, 'background:#ccc; font-weight:bold;' );
    }

    protected function addOption( &$options, $code, $name, $selected = false, $isGroup = false, $isClose = false, $style = '' )
    {
        $options[ $code ] = array(  'label'    => __( $name ),
                                    'selected' => $selected,
                                    'style'    => $style,
                                    'is_group' => $isGroup,
                                    'is_close' => $isClose,
                                    );
    }

    protected function isSelected( $check )
    {
        $value = $this->getValue();
        if ( is_array( $value ) )
            {
                return in_array( $check, $value );
            }
        return ( $check == $value );
    }

    protected function indent( $string, $indent )
    {
        $indent = $indent * 4;
        while ( $indent > 0 )
            {
                $string = '&nbsp;' . $string;
                $indent--;
            }
        return $string;
    }

    function getScopeId( $code )
    {
        $scopeId = FALSE;

        if ( $code == 'default' )
            {
                $scopeId = $this->storeManager->getStore( \Magento\Store\Model\Store::ADMIN_CODE )->getStoreId();
            }
        elseif ( stripos( $code, 'store_' ) === 0 )
            {
                $scopeId = substr_replace( $code, '', 0, strlen( 'store_' ) );
                $scopeId = $this->storeManager->getStore( $scopeId )->getStoreId();
            }
        elseif ( stripos( $code, 'website_' ) === 0 )
            {
                $scopeId = substr_replace( $code, '', 0, strlen( 'website_' ) );
                $scopeId = $this->storeManager->getWebsite( $scopeId )->getId();
            }

        return $scopeId;
    }

    function getScope( $code )
    {
        if ( $code == 'default' )
            {
                return \Magento\Store\Model\Store::ADMIN_CODE;
            }
        elseif ( stripos( $code, 'store_' ) === 0 )
            {
                return 'store';
            }
        elseif ( stripos( $code, 'website_' ) === 0 )
            {
                return 'website';
            }
        return false;
    }

    function getScopeCode( $code )
    {
        $parts = explode( '_', $code );
        if ( count ( $parts ) == 1 )
            {
                return $code;
            }
        $scope = array_shift( $parts );
        $result = implode( '_', $parts );
        return $result;
    }

}