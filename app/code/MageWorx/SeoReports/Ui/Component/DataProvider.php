<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Ui\Component;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \MageWorx\SeoReports\Model\ConfigInterface
     */
    protected $reportConfig;

    /**
     * @param \MageWorx\SeoReports\Model\ConfigInterface $reportConfig
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoReports\Model\ConfigInterface $reportConfig,
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->reportConfig = $reportConfig;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @return void
     */
    protected function prepareUpdateUrl()
    {
        parent::prepareUpdateUrl();

        $params = $this->request->getParams();

        if (!empty($params['store_id'])) {

            $filters = array_merge(
                ['store_id'],
                array_column($this->reportConfig->getDuplicateColumnData(), 'param_column')
            );

            foreach ($filters as $filter) {
                if (empty($params[$filter])) {
                    continue;
                }

                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s',
                    rtrim($this->data['config']['update_url'], '/') . '/',
                    $filter,
                    $params[$filter]
                );

                $this->addFilter(
                    $this->filterBuilder->setField($filter)->setValue($params[$filter])->setConditionType('eq')->create(
                    )
                );
            }
        }
    }
}
