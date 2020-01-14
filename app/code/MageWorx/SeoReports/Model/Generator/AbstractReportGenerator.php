<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Generator;

abstract class AbstractReportGenerator implements \MageWorx\SeoReports\Model\GeneratorInterface
{
    /**
     * @var \MageWorx\SeoReports\Model\DataProvider
     */
    protected $dataProvider;

    /**
     * @var \MageWorx\SeoReports\Model\ConfigInterface
     */
    protected $reportConfig;

    /**
     * @var \MageWorx\SeoReports\Model\ResourceModel\CategoryReport
     */
    protected $reportResource;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $relatedEntityClass;

    /**
     * Format example:
     * [
     *      '{store_id}' => [
     *          'prepared_title' => [
     *              'from' => 'One',
     *              'to' => 'Two'
     *          ],
     *          'prepared_name' => [
     *              'to' => 'New Name'
     *          ]
     *      ], ...
     * ]
     *
     * @var array
     */
    protected $conditionsData = [];

    /**
     * AbstractReportGenerator constructor.
     *
     * @param \MageWorx\SeoReports\Model\DataProvider $dataProvider
     * @param \MageWorx\SeoReports\Model\ConfigInterface $reportConfig
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $reportResource
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoReports\Api\ReportInterface $reportModel
     * @param string $relatedEntityClass
     */
    public function __construct(
        \MageWorx\SeoReports\Model\DataProvider $dataProvider,
        \MageWorx\SeoReports\Model\ConfigInterface $reportConfig,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $reportResource,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoReports\Api\ReportInterface $reportModel,
        $relatedEntityClass
    ) {
        $this->dataProvider       = $dataProvider;
        $this->reportConfig       = $reportConfig;
        $this->reportResource     = $reportResource;
        $this->storeManager       = $storeManager;
        $this->resource           = $resource;
        $this->connection         = $resource->getConnection();
        $this->relatedEntityClass = $relatedEntityClass;

        if (!$this->relatedEntityClass) {
            throw new \InvalidArgumentException('Related Entity Class is missed for Report Generator');
        }
    }

    /**
     * @return mixed
     */
    abstract protected function fillEntitiesPreparedData();

    /**
     * @param int $referenceId
     * @param int $storeId
     * @return mixed
     */
    abstract protected function getReportByReferenceAndStoreId($referenceId, $storeId);

    /**
     * @param int $referenceId
     * @return mixed
     */
    abstract protected function getReportCollectionByReference($referenceId);

    /**
     * @return string
     */
    abstract protected function getReportTableName();

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject|null $entity
     */
    public function generate($entity = null)
    {
        if ($entity) {
            $this->fillSingleEntityPreparedData($entity);
        } else {
            $this->clearData();
            $this->fillEntitiesPreparedData();
        }

        $this->calculateDuplicates($entity);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $entity
     * @param array $keepForStores
     * @throws \Exception
     */
    public function regenerateByRemoval($entity, $keepForStores = [])
    {
        $origStoreId   = $entity->getStoreId();
        $origIsDeleted = $entity->getIsDeleted();

        $reportCollection = $this->getReportCollectionByReference($entity->getId());

        foreach ($reportCollection as $report) {

            if (in_array($report->getStoreId(), $keepForStores)) {
                continue;
            }

            $entity->setStoreId($report->getStoreId());
            $entity->isDeleted(true);
            $newData = $this->getPreparedDataFullFormat($entity);
            $this->composeConditionData($entity->getStoreId(), $newData, $report);
            $this->reportResource->delete($report);
            $this->calculateDuplicates($entity);
        }

        $entity->setStoreId($origStoreId);
        $entity->isDeleted($origIsDeleted);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $entity
     * @throws \Exception
     */
    protected function fillSingleEntityPreparedData($entity)
    {
        if ($this->isNeedGenerationForSingleItem($entity)) {

            $report = $this->getReportByReferenceAndStoreId($entity->getId(), $entity->getStoreId());

            if ($this->isRelatedEntity($entity)) {
                $newData = $this->getPreparedDataFullFormat($entity);
            } else {
                if (!$report->getId()) {
                    // Regeneration of report is needed
                }
                $newData = $this->getPreparedData($entity, $this->reportConfig);
            }

            $this->composeConditionData($entity->getStoreId(), $newData, $report);
            $report->addData($newData);
            $this->reportResource->save($report);
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $entity
     * @return array
     */
    protected function getPreparedDataFullFormat($entity)
    {
        return $this->dataProvider->getPreparedDataFullFormat($entity, $this->reportConfig);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $entity
     * @return array
     */
    protected function getPreparedData($entity)
    {
        return $this->dataProvider->getPreparedData($entity, $this->reportConfig);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $entity
     * @return bool
     */
    protected function isNeedGenerationForSingleItem($entity)
    {
        if (!$this->isRelatedEntity($entity)) {
            return true;
        }

        foreach ($this->getFieldList() as $field) {
            if ($entity->dataHasChangedFor($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    protected function clearData()
    {
        $this->connection->truncateTable($this->resource->getTableName($this->getReportTableName()));
    }

    /**
     * Calculate duplicates using config and update report table
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject|null $entity
     */
    protected function calculateDuplicates($entity = null)
    {
        foreach ($this->reportConfig->getDuplicateColumnData() as $columns) {

            /** @var \Magento\Store\Model\Store $store */
            foreach ($this->storeManager->getStores() as $store) {

                $column    = $this->connection->quoteIdentifier($columns['column']);
                $condition = $subQueryCondition = '';

                if ($entity) {

                    if (empty($this->conditionsData[$store->getId()][$columns['column']])) {
                        continue;
                    }

                    $condition = 'AND `srp`.' . $column . ' IN(';

                    foreach ($this->conditionsData[$store->getId()][$columns['column']] as $value) {
                        $condition .= $this->connection->quote($value) . ',';
                    }

                    $condition         = rtrim($condition, ',') . ")";
                    $subQueryCondition = \str_replace('`srp`.', '', $condition);
                }

                $realTableName   = $this->resource->getTableName($this->getReportTableName());
                $duplicateColumn = $this->connection->quoteIdentifier($columns['duplicate_column']);

                $sql = "UPDATE `" . $realTableName . "` AS srp,
                        (SELECT " . $column . ", `store_id`, COUNT(*) AS dupl_count 
                        FROM `" . $realTableName . "` 
                        WHERE `store_id`=" . (int)$store->getId() . " AND " . $column . "!='' " . $subQueryCondition . "
                        GROUP BY " . $column . ") AS srpr
                        SET `srp`." . $duplicateColumn . " = `srpr`.`dupl_count`
                        WHERE `srp`." . $column . "=`srpr`." . $column . " AND `srp`.`store_id`=`srpr`.`store_id`  
                        AND `srp`." . $column . "!='' AND `srp`.`store_id`=" . (int)$store->getId() . ' ' . $condition;

                $this->connection->query($sql);
            }
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject|null $entity
     * @return bool
     */
    protected function isRelatedEntity($entity)
    {
        return ($entity instanceof $this->relatedEntityClass);
    }

    /**
     * @param int $storeId
     * @param array $newData
     * @param array $report
     */
    protected function composeConditionData($storeId, $newData, $report)
    {
        foreach ($this->reportConfig->getDuplicateColumnData() as $columnDatum) {

            $identifier = $columnDatum['column'];

            if (\array_key_exists($identifier, $newData)) {

                if ($newData[$identifier] != $report[$identifier]) {

                    if ($report[$identifier]) {
                        $this->conditionsData[$storeId][$identifier]['from'] = $report[$identifier];
                    }

                    if ($newData[$identifier]) {
                        $this->conditionsData[$storeId][$identifier]['to'] = $newData[$identifier];
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getFieldList()
    {
        return $this->reportConfig->getFieldList();
    }
}