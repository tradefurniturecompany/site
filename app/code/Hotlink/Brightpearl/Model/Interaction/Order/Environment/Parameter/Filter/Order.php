<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Environment\Parameter\Filter;

class Order extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter\Magento
{
    function getDefault()
    {
        $filter = parent::getDefault();
        $filter->setField( 'increment_id' )->setIdentifiers('')->setModel( '\Magento\Sales\Model\Order' );
        return $filter;
    }

    function getName()
    {
        return 'Order increment id';
    }

    function getIdsNote()
    {
        return 'Single value, multiple values separated with a comma (csv), range of values <code class="php">...10000009</code> or <code class="php">10000003...10000009</code> or <code class="php">10000008...</code>, a combinations of the above separated with a comma <code class="php">...10000004,10000007...10000012,10000023,10000039...</code>. To export all orders use <code class="php">*</code> (asterisk).';
    }
}