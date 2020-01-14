<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 */
class DuplicateAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['rule_id'])) {
                    $item[$this->getData('name')] = [
                        'duplicate' => [
                            'href' => $this->urlBuilder->getUrl(
                                'amasty_payrestriction/rule/duplicate',
                                ['id' => $item['rule_id']]
                            ),
                            'label' => __('Duplicate'),
                            'hidden' => false,
                        ],
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                'amasty_payrestriction/rule/edit',
                                ['id' => $item['rule_id']]
                            ),
                            'label' => __('Edit'),
                            'hidden' => true,
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
