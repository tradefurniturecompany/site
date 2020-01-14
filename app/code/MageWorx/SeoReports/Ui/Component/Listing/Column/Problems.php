<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Store
 */
class Problems extends Column
{
    /**
     * @var \MageWorx\SeoReports\Model\ConfigInterface
     */
    protected $reportConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \MageWorx\SeoReports\Helper\Data
     */
    protected $helperData;

    /**
     * @var
     */
    protected $backendUrl;

    /**
     * @var array
     */
    protected $problemConfig = [];

    /**
     * Problems constructor.
     *
     * @param \MageWorx\SeoReports\Model\ConfigInterface $reportConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MageWorx\SeoReports\Helper\Data $helperData
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoReports\Model\ConfigInterface $reportConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorx\SeoReports\Helper\Data $helperData,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->reportConfig  = $reportConfig;
        $this->helperData    = $helperData;
        $this->backendUrl    = $backendUrl;
        $this->objectManager = $objectManager;
    }

    /**
     * Prepare Options using report's config
     */
    public function prepare()
    {
        parent::prepare();

        if ($this->getOptions() instanceof \MageWorx\SeoReports\Model\Source\DynamicProblems) {

            $reportConfig = $this->reportConfig->getConfigProblemSections();

            if (!empty($reportConfig[$this->getName()])) {

                $config  = $this->getData('config');
                $options = [];

                foreach ($reportConfig[$this->getName()] as $problemType => $problemConfig) {

                    if ($problemType == 'duplicate') {
                        $options[] = [
                            'value' => 'duplicate',
                            'label' => __('Duplicated')
                        ];
                    } elseif ($problemType == 'missing') {
                        $options[] = [
                            'value' => 'missing',
                            'label' => __('Missing')
                        ];
                    } elseif ($problemType == 'length') {
                        $options[] = [
                            'value' => 'length',
                            'label' => __('Length')
                        ];
                    }
                }

                $config['options'] = $options;
                $this->setData('config', $config);
            }
        }
    }

    /**
     * Prepare Data Source using report's config
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $this->getPreparedConfig();

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {

            $problems = [];

            $config = $this->reportConfig->getConfigProblemSections();

            if (!empty($config[$fieldName])) {
                $problems[] = $this->getDuplicateProblemText($item, $config[$fieldName]);
                $problems[] = $this->getLengthProblemText($item, $config[$fieldName]);
                $problems[] = $this->getMissingProblemText($item, $config[$fieldName]);

                $problems = array_filter($problems);

                $item[$fieldName] = implode('<br/>', $problems);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return \Magento\Framework\Phrase|null|string
     */
    protected function getDuplicateProblemText(&$item, $fieldConfig)
    {
        $error               = null;
        $duplicateFiled      = null;
        $duplicateLink       = null;
        $duplicateParamFiled = null;

        if (!empty($fieldConfig['duplicate']['field'])) {
            $duplicateFiled = $fieldConfig['duplicate']['field'];
        }

        if (!empty($fieldConfig['duplicate']['link'])) {
            $duplicateLink = $fieldConfig['duplicate']['link'];
        }

        if (!empty($fieldConfig['duplicate']['param_field'])) {
            $duplicateParamFiled = $fieldConfig['duplicate']['param_field'];
        }

        if ($duplicateFiled && $duplicateLink && $item[$duplicateFiled] > 1) {

            if ($duplicateParamFiled) {

                $params = [
                    'store_id'           => !empty($item['store_id_orig']) ? $item['store_id_orig'] : $item['store_id'],
                    $duplicateParamFiled => $item[$duplicateParamFiled]
                ];

                $error =
                    '<a href="' . $this->backendUrl->getUrl($duplicateLink, $params) . '">' .
                    __('Duplicate (%1)', $item[$duplicateFiled]) .
                    '</a>';
            } else {
                $error = __('Duplicate (%1)', $item[$duplicateFiled]);
            }
        }

        return $error;
    }

    /**
     * @param array $item
     * @return \Magento\Framework\Phrase|null
     */
    protected function getLengthProblemText(&$item, $fieldConfig)
    {
        $error          = null;
        $lengthFiled    = null;
        $lengthProvider = null;

        if (!empty($fieldConfig['length']['field'])) {
            $lengthFiled = $fieldConfig['length']['field'];
        }

        if (!empty($fieldConfig['length']['length_provider'])) {
            $lengthProviderClass = $fieldConfig['length']['length_provider'];
            /** @var \MageWorx\SeoReports\Model\LengthDataProviderInterface $lengthProvider */
            $lengthProvider = $this->objectManager->get($lengthProviderClass);
        }

        if ($lengthFiled && $lengthProvider) {

            $maxLength = $lengthProvider->getMaxLength();
            $minLength = $lengthProvider->getMinLength();

            if (is_numeric($maxLength) && $maxLength > 0 && $item[$lengthFiled] > $maxLength) {
                $error = __('Long %1', $item[$lengthFiled]);
            }

            if (is_numeric($minLength) && $minLength > 0 && $item[$lengthFiled] < $minLength) {

                $error = __('Short %1', $item[$lengthFiled]);
            }
        }

        return $error;
    }

    /**
     * @param array $item
     * @return \Magento\Framework\Phrase|null
     */
    protected function getMissingProblemText(&$item, $fieldConfig)
    {
        $error        = null;
        $missingFiled = !empty($fieldConfig['missing']['field']) ? $fieldConfig['missing']['field'] : null;
        $fieldType    = !empty($fieldConfig['missing']['field_type']) ? $fieldConfig['missing']['field_type'] : null;

        if ($missingFiled && $fieldType) {
            if ($fieldType == 'text' && $item[$missingFiled] === '') {
                $error = __('Missing');
            } elseif ($fieldType == 'length' && (int)$item[$missingFiled] === 0) {
                $error = __('Missing');
            }
        }

        return $error;
    }
}
