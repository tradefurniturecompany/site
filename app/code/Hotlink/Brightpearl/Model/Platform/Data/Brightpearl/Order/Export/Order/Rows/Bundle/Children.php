<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Bundle;

class Children extends \Hotlink\Brightpearl\Model\Platform\Data
{

    /**
     * @var \Hotlink\Framework\Helper\Convention\Check
     */
    protected $conventionCheckHelper;

    function __construct(
        \Magento\Framework\Simplexml\ElementFactory $xmlFactory,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Convention\Data\Helper $conventionDataHelperHelper,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Helper\Convention\Data $conventionDataHelper,
        \Hotlink\Framework\Model\Api\DataFactory $dataFactory,

        \Hotlink\Framework\Helper\Convention\Check $conventionCheckHelper,
        array $data = []
    )
    {
        $this->conventionCheckHelper = $conventionCheckHelper;

        parent::__construct(
            $xmlFactory,
            $factoryHelper,
            $reportFactory,
            $exceptionHelper,
            $conventionDataHelperHelper,
            $configMap,
            $conventionDataHelper,
            $dataFactory,
            $data );
    }

    protected function _map_object_magento( \Magento\Sales\Model\Order\Item $item )
    {
        $children = $item->getChildrenItems();
        foreach ( $children as $child )
            {
                if ( $obj = $this->getObject( $child, ucfirst($child->getProductType()), true ) )
                    {
                        if ( $obj['price'] )
                            {
                                $obj['price']['amountExcludingTax'] = 0.0;
                                $obj['price']['amountIncludingTax'] = 0.0;
                                $obj['price']['tax']                = 0.0;
                            }
                        if ( $obj['total'] )
                            {
                                $obj['total']['amountExcludingTax'] = 0.0;
                                $obj['total']['amountIncludingTax'] = 0.0;
                                $obj['total']['tax']                = 0.0;
                            }
                        $obj['productOptions'] = (object)array();
                        $obj['children'] = array();
                        $this[] = $obj;
                    }
            }
    }

    function getChildClassDefault( $key )
    {
        $class = '\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\\' . $key;
        if ( $this->conventionCheckHelper->exists( $class ) )
            {
                return $class;
            }
        $this->getReport()->warn( $this->annotate( "The product type $key is not defined, using simple" ) );
        return '\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple';
    }

}