<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer\Customer;

class Group extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Magento\Customer\Model\Config\Source\Group
     */
    protected $customerSourceGroup;

    public function __construct(
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

    public function setInputName( $value )
    {
        return $this->setName( $value );
    }

    public function clean( $item )
    {
        if ( is_array( $item ) )
            {
                $result = [];
                foreach ( $item as $key => $val )
                    {
                        $result[ $key ] = $this->clean( $val );
                    }
                return $result;
            }
        return $this->escapeJsQuote( $item );
    }

    public function _toHtml()
    {
        if ( !$this->getOptions() )
            {
                $options = $this->customerSourceGroup->toOptionArray();
                $cleaned = $this->clean( $options );
                foreach ( $cleaned as $item )
                    {
                        if ( array_key_exists( 'value', $item ) )
                            {
                                $value = $item[ 'value' ];
                                $label = $item[ 'label' ];
                                $this->addOption( $value, $label );
                            }
                    }
            }
        return parent::_toHtml();
    }

}
