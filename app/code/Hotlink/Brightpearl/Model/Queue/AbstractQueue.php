<?php
namespace Hotlink\Brightpearl\Model\Queue;

abstract class AbstractQueue extends \Magento\Framework\Model\AbstractModel implements \Hotlink\Framework\Model\Report\IReport
{

    protected $_report;
    protected $reportHelper;

    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->reportHelper = $reportHelper;
        parent::__construct( $context, $registry, $resource, $resourceCollection, $data );
    }

    function status( $message )
    {
        $report = $this->getReport();

        $fields = [];
        foreach ( $this->getData() as $key => $value )
            {
                $fields[] = $key . "=" . ( is_null( $value ) ? 'null' : $value );
            }
        $report
            ->debug( $message )
            ->indent()
            ->debug(implode(", ", $fields))
            ->unindent();
    }

    //
    //  IReport
    //
    function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->reportHelper->create( $this ) );
            }
        return $this->_report;
    }

    function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    abstract function getReportSection();

}
