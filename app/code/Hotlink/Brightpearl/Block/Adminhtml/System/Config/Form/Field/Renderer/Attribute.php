<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Field\Renderer;

class Attribute extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \Hotlink\Framework\Model\Config\Field\Product\Attributes
     */
    protected $interactionConfigFieldProductAttributes;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Hotlink\Framework\Model\Config\Field\Product\Attributes $interactionConfigFieldProductAttributes,
        array $data = []
    ) {
        $this->interactionConfigFieldProductAttributes = $interactionConfigFieldProductAttributes;
        parent::__construct(
            $context,
            $data
        );
    }

    public function setInputName( $value )
    {
        return $this->setName( $value );
    }

    public function _toHtml()
    {
        if ( !$this->getOptions() ) {
            if ( !$this->getOptions()) {
                $skip = array('select', 'textarea', 'date', 'gallery', 'media_image');

                $attributes = $this->interactionConfigFieldProductAttributes->getAttributes();
                foreach ($attributes as $attribute) {
                    if (!in_array($attribute->getFrontendInput(), $skip) &&
                        !($attribute->getIsGlobal() == \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE)) {

                        $this->addOption( $attribute->getAttributeCode(), $attribute->getAttributeCode() );
                    }
                }
            }
        }
        return parent::_toHtml();
    }
}
