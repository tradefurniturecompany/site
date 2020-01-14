<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import;

use \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;


class CsvHandler extends CsvHandlerAbstract
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
     * @var \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity
     */
    protected $redirectTypeEntityOptions;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntityCode
     */
    protected $redirectTypeEntityCodeOptions;

    /**
     * CsvHandler constructor.
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
     * @param \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity $redirectTypeEntityOptions
     * @param \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntityCode $redirectTypeEntityCodeOptions
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity $redirectTypeEntityOptions,
        \MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntityCode $redirectTypeEntityCodeOptions
    ) {
        parent::__construct($csvProcessor);

        $this->redirectRepository            = $redirectRepository;
        $this->redirectFactory               = $redirectFactory;
        $this->redirectCollectionFactory     = $redirectCollectionFactory;
        $this->publicStores                  = $storeCollection->setLoadDefault(false);
        $this->redirectCollectionFactory     = $redirectCollectionFactory;
        $this->escaper                       = $escaper;
        $this->dataObjectFactory             = $dataObjectFactory;
        $this->storeManager                  = $storeManager;
        $this->redirectTypeEntityOptions     = $redirectTypeEntityOptions;
        $this->redirectTypeEntityCodeOptions = $redirectTypeEntityCodeOptions;
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
                    __('Missed Request Entity Type in line %1', $rowIndex + 2)
                );
            }

            if (!in_array($dataRow[0], $this->redirectTypeEntityCodeOptions->toArray())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid Request Entity Type code in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[1] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Request URL in line %1', $rowIndex + 2)
                );
            }

            if (strpos($dataRow[1], 'http') === 0) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The requested identifier can\'t start from "http".')
                );
            }

            if ($dataRow[2] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Target Entity Type in line %1', $rowIndex + 2)
                );
            }

            if (!in_array($dataRow[2], $this->redirectTypeEntityCodeOptions->toArray())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid Target Entity Type code in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[3] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Target Entity Identifier in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[0] == $dataRow[2] && $dataRow[1] == $dataRow[3]) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Request and target entities(URLs) can't be identical in line %1", $rowIndex + 2)
                );
            }

            if (!(in_array($dataRow[4], ['301', '302']))) {

                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid Redirect Code in line %1', $rowIndex + 2)
                );
            }

            if ($dataRow[5] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed Store ID in line %1', $rowIndex + 2)
                );
            }

            $uniqueKey = $dataRow[0] . '-!mw!-' . $dataRow[1] . '-!mw!-' . $dataRow[5];

            if (\array_key_exists($uniqueKey, $uniqueArray)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'Duplicate row (Request Entity Type, Request Entity Identifier and Store ID combination) was found in line %1',
                        $rowIndex + 2
                    )
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
        $requestedStoreCodes = array_column($data, '5');
        $requestedStoreCodes = array_unique($requestedStoreCodes);

        $storeIds = $this->publicStores->getColumnValues('store_id');

        $missedStoreIds = array_diff($requestedStoreCodes, $storeIds);

        foreach ($missedStoreIds as $key => $store) {
            if ($store === '0') {
                unset($missedStoreIds[$key]);
            }
        }

        if ($missedStoreIds) {

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested store IDs are not found: %1.',
                    $this->escaper->escapeHtml(implode(' ,', $missedStoreIds))
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
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
                [
                    CustomRedirect::REQUEST_ENTITY_TYPE,
                    CustomRedirect::REQUEST_ENTITY_IDENTIFIER,
                    CustomRedirect::TARGET_ENTITY_TYPE,
                    CustomRedirect::TARGET_ENTITY_IDENTIFIER,
                    CustomRedirect::REDIRECT_CODE,
                    CustomRedirect::STORE_ID
                ],
                array_values($dataRow)
            );

            $redirectTypeEntityCodes = array_flip($this->redirectTypeEntityCodeOptions->toArray());

            $storeId = $dataRow[CustomRedirect::STORE_ID];
            $stores  = ($storeId === '0') ? $storeOptionHash : [$storeId];


            $sourceRequestEntityType = $dataRow[CustomRedirect::REQUEST_ENTITY_TYPE];
            $sourceTargetEntityType = $dataRow[CustomRedirect::TARGET_ENTITY_TYPE];

            foreach ($stores as $storeId) {
                $dataRow[CustomRedirect::STORE_ID] = $storeId;

                $dataRow[CustomRedirect::REQUEST_ENTITY_TYPE] =
                    $redirectTypeEntityCodes[$sourceRequestEntityType];

                $dataRow[CustomRedirect::TARGET_ENTITY_TYPE] =
                    $redirectTypeEntityCodes[$sourceTargetEntityType];

                $uniqKeyForStore = $dataRow[CustomRedirect::REQUEST_ENTITY_TYPE] . '-!mw!-' .
                    $dataRow[CustomRedirect::REQUEST_ENTITY_IDENTIFIER];

                if (!empty($splitData[$storeId][$uniqKeyForStore])) {
                    $duplicates[$storeId] = $dataRow;
                } else {
                    $splitData[$storeId][$uniqKeyForStore] = $dataRow;
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
                    $duplicateData[CustomRedirect::REQUEST_ENTITY_IDENTIFIER]
                );
                $i++;
            }

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Duplicated redirects were detected (Request Entity Type together with Request Identifier must be unique both for the redirects with specific Store ID values and Store ID = "0" redirects): %1',
                    $report
                )
            );
        }

        return $splitData;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkDuplicatedRedirects(array $data)
    {
        $duplicatedUniqueParts = [];

        foreach ($data as $storeId => $storeData) {

            if (!$storeData) {
                continue;
            }

            $requestEntityTypeList = array_column($storeData, CustomRedirect::REQUEST_ENTITY_TYPE);

            foreach ($requestEntityTypeList as $requestEntityType) {

                $redirectCollection = $this->redirectCollectionFactory->create();
                $redirectCollection
                    ->addStoreFilter($storeId)
                    ->addFieldToFilter(
                        CustomRedirect::REQUEST_ENTITY_TYPE,
                        $requestEntityType
                    );

                $result = $redirectCollection->toArray();


                if (!empty($result['items'])) {
                    $redirectUniqueParts    = array_column(
                        $result['items'],
                        CustomRedirect::REQUEST_ENTITY_IDENTIFIER,
                        CustomRedirect::REDIRECT_ID
                    );
                    $requestDataUniqueParts = array_column($storeData, CustomRedirect::REQUEST_ENTITY_IDENTIFIER);

                    $duplicatedUniquePartsOnStore = array_intersect($redirectUniqueParts, $requestDataUniqueParts);

                    if ($duplicatedUniquePartsOnStore) {
                        $duplicatedUniqueParts[$storeId] = $duplicatedUniquePartsOnStore;
                    }
                }
            }
        }

        if ($duplicatedUniqueParts) {

            $redirectTypeEntityOptions = $this->redirectTypeEntityOptions->toArray();

            $i      = 1;
            $report = '';
            foreach ($duplicatedUniqueParts as $storeId => $duplicatedUnique) {

                foreach ($duplicatedUnique as $redirectId => $requestIdentifier) {

                    $report .= __(
                        '  %1) [Redirect from %2 (%3) on store (ID:%4) is identical with existent redirect (ID:%5)] ',
                        $i,
                        $redirectTypeEntityOptions[$requestEntityType],
                        $requestIdentifier,
                        $storeId,
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
                    ->setRedirectCode($dataRow[CustomRedirect::REDIRECT_CODE])
                    ->setRequestEntityType($dataRow[CustomRedirect::REQUEST_ENTITY_TYPE])
                    ->setRequestEntityIdentifier($dataRow[CustomRedirect::REQUEST_ENTITY_IDENTIFIER])
                    ->setStoreId($storeId)
                    ->setTargetEntityType($dataRow[CustomRedirect::TARGET_ENTITY_TYPE])
                    ->setTargetEntityIdentifier($dataRow[CustomRedirect::TARGET_ENTITY_IDENTIFIER])
                    ->setIsImported(true);

                $this->redirectRepository->save($redirect);
            }
        }
    }
}