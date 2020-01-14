<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import;

use \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

class CsvSimpleFormatHandler extends CsvHandlerAbstract
{
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $publicStores;

    /**
     * @var \MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface
     */
    protected $redirectRepository;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory
     */
    protected $redirectCollectionFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * CsvSimpleFormatHandler constructor.
     *
     * @param \MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface $redirectRepository
     * @param \MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory $redirectFactory
     * @param \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory $redirectCollectionFactory
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\SeoRedirects\Api\CustomRedirectRepositoryInterface $redirectRepository,
        \MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory $redirectFactory,
        \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory $redirectCollectionFactory,
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory $collectionFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($csvProcessor);

        $this->redirectRepository        = $redirectRepository;
        $this->redirectFactory           = $redirectFactory;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
        $this->publicStores              = $storeCollection->setLoadDefault(false);
        $this->redirectCollectionFactory = $redirectCollectionFactory;
        $this->escaper                   = $escaper;
        $this->dataObjectFactory         = $dataObjectFactory;
        $this->storeManager              = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateByDataFormat(array $data)
    {
        $uniqueArray = [];

        foreach ($data as $rowIndex => $dataRow) {

            if ($dataRow[0] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Request URL in line %1', $rowIndex + 2)
                );
            }

            if (strpos($dataRow[0], 'http') === 0) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The requested identifier can\'t start from "http".')
                );
            }

            if ($dataRow[1] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Target URL in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[0] == $dataRow[1]) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Request and target URLs can\'t be identical - line %1', $rowIndex + 2)
                );
            }

            if (!(in_array($dataRow[2], ['301', '302']))) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid redirect code in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[3] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed store code in line %1', $rowIndex + 2)
                );
            }

            $uniqueKey = $dataRow[0] . '-!mw!-' . $dataRow[3];

            if (\array_key_exists($uniqueKey, $uniqueArray)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Duplicate row (Request URL and Store Code combination) was found in line %1', $rowIndex + 2)
                );
            }

            $uniqueArray[$uniqueKey] = $dataRow;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateByDataValues(array $data)
    {
        $requestedStoreCodes = array_column($data, '3');
        $requestedStoreCodes = array_unique($requestedStoreCodes);

        $storeCodes       = $this->publicStores->getColumnValues('code');
        $missedStoreCodes = array_diff($requestedStoreCodes, $storeCodes);

        if ($missedStoreCodes) {

            if (count($missedStoreCodes) == 1 && array_shift($missedStoreCodes) == 'all') {

            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'The requested store code(s) is not found: %1.',
                        $this->escaper->escapeHtml(implode(' ,', $missedStoreCodes))
                    )
                );
            }
        }

        return true;
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function splitOnStoresAndCheckInternalDuplicates(array $data)
    {
        $storeOptionHash = [];

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($this->publicStores as $store) {
            $storeOptionHash[$store->getCode()] = $store->getId();
            $splitData[$store->getId()]         = [];
        }

        $duplicates = [];

        foreach ($data as $dataRow) {

            $dataRow = array_combine(
                ['request_entity_identifier', 'target_entity_identifier', 'redirect_code', 'store_id'],
                array_values($dataRow)
            );

            if ($dataRow['store_id'] == 'all') {
                foreach ($storeOptionHash as $storeId) {
                    $dataRow['store_id'] = $storeId;
                    if (!in_array($dataRow, $splitData[$storeId])) {
                        $splitData[$storeId][] = $dataRow;
                    } else {
                        $duplicates[$storeId] = $dataRow;
                    }
                }
            } else {
                $storeId             = $storeOptionHash[$dataRow['store_id']];
                $dataRow['store_id'] = $storeId;
                if (!in_array($dataRow, $splitData[$storeId])) {
                    $splitData[$storeId][] = $dataRow;
                } else {
                    $duplicates[$storeId] = $dataRow;
                }
            }
        }

        if (!empty($duplicates)) {

            $i      = 1;
            $report = '';

            foreach ($duplicates as $storeId => $duplicateData) {
                $report .= __(
                    '%1) [Store: %2, Request Identifier: %3]',
                    $i,
                    $storeId . ' (' . array_keys($storeOptionHash, $storeId)[0] . ')',
                    $duplicateData['request_entity_identifier']
                );
                $i++;
            }

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Duplicated redirects were detected (Request Identifier must be unique both for the redirects with specific Store ID values and Store ID = "all" redirects): %1',
                    $report
                )
            );
        }

        return $splitData;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkDuplicatedRedirects(array $data)
    {
        $duplicatedUniqueParts = [];

        foreach ($data as $storeId => $storeData) {

            if (!$storeData) {
                continue;
            }

            $redirectCollection = $this->redirectCollectionFactory->create();
            $redirectCollection
                ->addStoreFilter($storeId)
                ->addFieldToFilter(
                    CustomRedirect::REQUEST_ENTITY_TYPE,
                    CustomRedirect::REDIRECT_TYPE_CUSTOM
                );

            $result = $redirectCollection->toArray();

            if (!empty($result['items'])) {
                $redirectUniqueParts    = array_column($result['items'], 'request_entity_identifier', 'redirect_id');
                $requestDataUniqueParts = array_column($storeData, 'request_entity_identifier');

                $duplicatedUniquePartsOnStore = array_intersect($redirectUniqueParts, $requestDataUniqueParts);

                if ($duplicatedUniquePartsOnStore) {
                    $duplicatedUniqueParts[$storeId] = $duplicatedUniquePartsOnStore;
                }
            }
        }

        if ($duplicatedUniqueParts) {

            $i      = 1;
            $report = '';
            foreach ($duplicatedUniqueParts as $storeId => $duplicatedUnique) {

                foreach ($duplicatedUnique as $redirectId => $requestIdentifier) {

                    $report .= __(
                        '  %1) [Store ID: %2, Request Identifier: %3, Redirect ID: %4]',
                        $i,
                        $storeId,
                        $requestIdentifier,
                        $redirectId
                    );
                    $i++;
                }
            }

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Some imported redirects are identical to the existent redirects:%1. Please, remove them from the import file.',
                    $report
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function import(array $storeSplitData)
    {
        foreach ($storeSplitData as $storeId => $data) {
            foreach ($data as $dataRow) {

                $redirect = $this->redirectFactory->create();

                $redirect
                    ->setRedirectCode($dataRow['redirect_code'])
                    ->setRequestEntityType(CustomRedirect::REDIRECT_TYPE_CUSTOM)
                    ->setRequestEntityIdentifier($dataRow['request_entity_identifier'])
                    ->setStoreId($storeId)
                    ->setTargetEntityType(CustomRedirect::REDIRECT_TYPE_CUSTOM)
                    ->setTargetEntityIdentifier($dataRow['target_entity_identifier'])
                    ->setIsImported(true);

                $this->redirectRepository->save($redirect);
            }
        }
    }
}