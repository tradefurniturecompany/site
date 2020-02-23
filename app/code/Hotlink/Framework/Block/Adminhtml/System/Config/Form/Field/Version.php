<?php
namespace Hotlink\Framework\Block\Adminhtml\System\Config\Form\Field;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $moduleHelper;
    protected $reflectionHelper;

    function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hotlink\Framework\Helper\Module $moduleHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->reflectionHelper = $reflectionHelper;

        parent::__construct( $context, $data );
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->moduleHelper->getVersion( $this->getModule() );
        $element->setValue( $version );

        return parent::_getElementHtml( $element );
    }

    protected function getModule()
    {
        return $this->reflectionHelper->getModule( $this );
    }
}
