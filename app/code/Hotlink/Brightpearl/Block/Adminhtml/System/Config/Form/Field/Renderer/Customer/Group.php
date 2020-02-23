<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Customer;

class Group extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Magento\Customer\Model\Config\Source\Group
     */
    protected $customerSourceGroup;

    function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Customer\Model\Customer\Source\Group $customerSourceGroup,
        array $data = []
    ) {
        $this->customerSourceGroup = $customerSourceGroup;
        parent::__construct(
            $context,
            $data
        );
    }

    function setInputName( $value )
    {
        return $this->setName( $value );
    }

    function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->customerSourceGroup->toOptionArray();
                foreach ( $options as $item )
                    {
                        if ( array_key_exists( 'value', $item ) )
                            {
                                $value = $this->escapeJsQuote( $item[ 'value' ] );
                                $label =  $this->escapeJsQuote( $item[ 'label' ] );
                                $this->addOption( $value, $label );
                            }
                    }
            }
        return parent::_toHtml();
    }

}
