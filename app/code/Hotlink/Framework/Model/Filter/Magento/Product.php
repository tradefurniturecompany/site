<?php
namespace Hotlink\Framework\Model\Filter\Magento;

class Product extends \Hotlink\Framework\Model\Filter\Magento
{

    /**
     * @var \Hotlink\Framework\Model\Config\Field\Identifier\Product\Source
     */
    protected $interactionConfigFieldIdentifierProductSource;

    public function __construct(
        \Hotlink\Framework\Model\Config\Field\Identifier\Product\Source $interactionConfigFieldIdentifierProductSource
    )
    {
        $this->interactionConfigFieldIdentifierProductSource = $interactionConfigFieldIdentifierProductSource;
        $this->_model = 'catalog/product';
        $this->_field = 'sku';
    }

    public function getFields()
    {
        return $this->interactionConfigFieldIdentifierProductSource->getOptions();
    }

}