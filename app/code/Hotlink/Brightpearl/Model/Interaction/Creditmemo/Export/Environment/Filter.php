<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment;

class Filter extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter\Magento
{

    function getDefault()
    {
        $filter = parent::getDefault();
        $filter->setField( 'increment_id' )->setIdentifiers('')->setModel( '\Magento\Sales\Model\Order\Creditmemo' );
        return $filter;
    }

    function getName()
    {
        return 'Creditmemo increment id';
    }

    function getIdsNote()
    {
        return 'Single value, multiple values separated with a comma (csv). Eg. <code class="php">10000004,10000007</code>. To export all creditmemos use <code class="php">*</code> (asterisk).';
    }

}