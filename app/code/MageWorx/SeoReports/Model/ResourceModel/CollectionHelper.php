<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\ResourceModel;

class CollectionHelper
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CollectionHelper constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->request       = $request;
        $this->objectManager = $objectManager;
    }

    /**
     * Add condition to collection to hide the items without any problems
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param \MageWorx\SeoReports\Model\ConfigInterface $config
     */
    public function addProblemsFilter($collection, $config)
    {
        $filters = [];

        foreach ($config->getConfigProblemSections() as $problemSection) {

            if (is_array($problemSection) && $problemSection) {

                $connection = $collection->getConnection();

                foreach ($problemSection as $problemType => $problemConfig) {
                    if ($problemType == 'length') {
                        $lengthProvider = $this->objectManager->get($problemConfig['length_provider']);
                        $maxLength      = $lengthProvider->getMaxLength();

                        if ($lengthProvider && is_numeric($maxLength) && $maxLength > 0) {
                            $filters[] = $connection->quoteInto("{$problemConfig['field']}>?", $maxLength);
                        }
                    }

                    if ($problemType == 'duplicate') {
                        $filters[] = $connection->quoteInto("{$problemConfig['field']}>?", 1);
                    }

                    if ($problemType == 'missing') {

                        if ($problemConfig['field_type'] == 'text') {
                            $filters[] = $connection->quoteInto("{$problemConfig['field']}=?", '');
                        }

                        if ($problemConfig['field_type'] == 'length') {
                            $filters[] = $connection->quoteInto("{$problemConfig['field']}=?", 0);
                        }
                    }
                }
            }
        }

        $condition = implode(' OR ', $filters);
        $collection->getSelect()->where($condition);
    }

    /**
     * Retrieve list of specific conditions based on the config and current field with condition
     *
     * @param \MageWorx\SeoReports\Model\ConfigInterface $config
     * @param string $field
     * @param array $condition
     * @return array
     */
    public function convertFiltersByConfig($config, $field, $condition)
    {
        $data = [];

        if (empty($condition['in'])) {
            return $data;
        }

        foreach ($config->getConfigProblemSections() as $filterField => $fieldProblems) {
            if ($filterField !== $field) {
                continue;
            }

            foreach ($condition['in'] as $problemType) {

                if (empty($fieldProblems[$problemType])) {
                    continue;
                }

                if ($problemType == 'duplicate' && !empty($fieldProblems['duplicate']['field'])) {
                    $data[] = ['field' => $fieldProblems['duplicate']['field'], 'condition' => ['gt' => 1]];
                }

                if ($problemType == 'length'
                    && !empty($fieldProblems['length']['field'])
                    && !empty($fieldProblems['length']['length_provider'])
                ) {
                    /** @var \MageWorx\SeoReports\Model\LengthDataProviderInterface $lengthProvider */
                    $lengthProvider = $this->objectManager->get($fieldProblems['length']['length_provider']);
                    $maxLength      = $lengthProvider->getMaxLength();

                    if ($lengthProvider && is_numeric($maxLength) && $maxLength > 0) {
                        $data[] = ['field' => $fieldProblems['length']['field'], 'condition' => ['gt' => $maxLength]];
                    }
                }

                if ($problemType == 'missing'
                    && !empty($fieldProblems['missing']['field'])
                    && !empty($fieldProblems['missing']['field_type'])
                ) {
                    if ($fieldProblems['missing']['field_type'] == 'text') {
                        $data[] = ['field' => $fieldProblems['missing']['field'], 'condition' => ['eq' => '']];
                    }

                    if ($fieldProblems['missing']['field_type'] == 'length') {
                        $data[] = ['field' => $fieldProblems['missing']['field'], 'condition' => ['eq' => 0]];
                    }
                }
            }
        }

        return $data;
    }
}
