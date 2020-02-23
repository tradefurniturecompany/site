<?php
namespace Hotlink\Brightpearl\Model\Interaction\Skus\Export;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    /**
     * @var \Hotlink\Brightpearl\Helper\Api\Service\Workflow
     */
    protected $brightpearlApiServiceWorkflowHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $catalogResourceModelProductCollectionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Brightpearl\Helper\Api\Service\Workflow $brightpearlApiServiceWorkflowHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory
    ) {
        $this->brightpearlApiServiceWorkflowHelper = $brightpearlApiServiceWorkflowHelper;
        $this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;

        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl Skus Export';
    }

    function execute()
    {
        $report = $this->getReport();
        $environment = $this->getEnvironment();
        $storeId = $environment->getStoreId();
        $accountCode = $environment->getAccountCode();
        $api = $this->brightpearlApiServiceWorkflowHelper;

        $report = $report->info("Loading Magento SKUs");

        $collection = $this->catalogResourceModelProductCollectionFactory->create();
        $table = $collection->getEntity()->getEntityTable();
        $connection = $collection->getConnection();

        $select = $connection->select();
        $select->from($table, array('sku'));

        $skus = $connection->fetchAssoc($select);

        if (!empty($skus)) {
            $skus = array_keys($skus);
            $nrSkus = count($skus);

            $report->addReference($skus);

            try {

                $report->__invoke($api, 'postInstanceProducts', $storeId, $accountCode, $skus);
                $report->setSuccess($nrSkus);

            } catch (\Hotlink\Framework\Model\Exception\Base $fe) {
                $report->error($fe->getMessage());
                $report->setFail($nrSkus);
            }
        }
        else {
            $report->info("No SKUs found");
        }

        return $this;
    }
}
