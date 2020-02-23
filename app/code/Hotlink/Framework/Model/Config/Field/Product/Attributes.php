<?php
namespace Hotlink\Framework\Model\Config\Field\Product;

class Attributes
{

    protected $_attributes = false;
    protected $_all = false;
    protected $_with_options = false;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $catalogResourceModelProductAttributeCollectionFactory;

    function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $catalogResourceModelProductAttributeCollectionFactory
    ) {
        $this->catalogResourceModelProductAttributeCollectionFactory = $catalogResourceModelProductAttributeCollectionFactory;
    }

    function getAttributes()
    {
        if ( !$this->_attributes )
            {
                $this->_attributes = $this->catalogResourceModelProductAttributeCollectionFactory->create();
            }
        return $this->_attributes;
    }

    function toOptionArray()
    {
        if ( !$this->_all )
            {
                $result = array();
                foreach ( $this->getAttributes() as $attribute )
                    {
                        $result[] = array(
                                          'value' => $attribute->getAttributeCode(),
                                          'label' => $attribute->getAttributeCode()
                                          );
                    }
                array_unshift( $result, array( 'value' => '',
                                               'label' => __( '--' ) ) );
                $this->_all = $result;
            }
        return $this->_all;
    }

    function filterWithOptions()
    {
        if ( !$this->_with_options )
            {
                $result = array();
                foreach ( $this->getAttributes() as $attribute )
                    {
                        if ( $attribute->usesSource() )
                            {
                                $result[] = array(
                                                  'value' => $attribute->getAttributeCode(),
                                                  'label' => $attribute->getAttributeCode()
                                                  );
                            }
                    }
                $this->_with_options = $result;
            }
        return $this->_with_options;
    }

}
