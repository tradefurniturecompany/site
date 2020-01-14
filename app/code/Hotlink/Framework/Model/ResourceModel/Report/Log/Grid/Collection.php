<?php
namespace Hotlink\Framework\Model\ResourceModel\Report\Log\Grid;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $_initialised;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = 'hotlink_framework_report_log',
        $resourceModel = '\Hotlink\Framework\Model\ResourceModel\Report\Log'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }


    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        // Add duration as custom column to select
        $now = date('Y-m-d H:i:s');
        $this->addExpressionFieldToSelect('duration', "timediff( ifnull( ended, '" . $now . "' ), started )", []);

        // Add progress as custom column to select
        $this->addExpressionFieldToSelect('progress', "concat( success, 's / ', fail, 'f' )", []);

        $this->addExpressionFieldToSelect( 'reference', "LEFT( reference, 38)", '' );

        return $this;
    }

    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {

            $currentOrders = $this->_orders;
            $newOrders = [];

            foreach ($currentOrders as $field => $direction) {

                if ($field == 'trigger') {
                    $newOrders[ '`trigger`' ] = $direction; // surround trigger with backticks to avoid sql error
                }
                else {
                    $newOrders[ $field ] = $direction;
                }
            }

            $this->_orders = $newOrders;
        }

        return parent::_renderOrders();
    }
}
