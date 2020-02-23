<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Filter extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter\Magento
{

    function getDefault()
    {
        $filter = parent::getDefault();
        $filter->setField( 'sku' )->setIdentifiers('')->setModel( '\Magento\Catalog\Model\Product' );
        return $filter;
    }

    function getName()
    {
        return 'SKUs';
    }

    function getIdsNote()
    {
        return 'Separate SKUs with a comma (csv)';
    }

}
