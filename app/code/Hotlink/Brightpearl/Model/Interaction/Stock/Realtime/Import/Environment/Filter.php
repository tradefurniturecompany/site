<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment;

class Filter extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter\Magento
{

    public function getDefault()
    {
        $filter = parent::getDefault();
        $filter->setField( 'sku' )->setIdentifiers('')->setModel( '\Magento\Catalog\Model\Product' );
        return $filter;
    }

    public function getName()
    {
        return 'SKUs';
    }

    public function getIdsNote()
    {
        return 'Separate SKUs with a comma (csv)';
    }

}
