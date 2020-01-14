<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\Import;

abstract class CsvHandlerAbstract
{
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * CsvHandlerAbstract constructor.
     *
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    abstract protected function validateByDataFormat(array $data);

    /**
     * @param array $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    abstract protected function validateByDataValues(array $data);

    /**
     * Splits redirects by stores and converts request and target entity types to database format, ex: product_id => 1
     * Checks duplicates in the imported data
     *
     * @param array $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    abstract protected function splitOnStoresAndCheckInternalDuplicates(array $data);

    /**
     * Checks duplicates for imported data and data from database
     *
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    abstract protected function checkDuplicatedRedirects(array $data);

    /**
     * @param array $storeSplitData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    abstract protected function import(array $storeSplitData);

    /**
     * @param array $file file info retrieved from $_FILES array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $importData = $this->csvProcessor->getData($file['tmp_name']);

        if (count($importData) < 2) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Data for import not found')
            );
        }

        array_shift($importData);
        array_walk_recursive($importData, [$this, 'trim']);

        if ($this->validateData($importData)) {
            $storeSplitData = $this->splitOnStoresAndCheckInternalDuplicates($importData);
            $this->checkDuplicatedRedirects($storeSplitData);
            $this->import($storeSplitData);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateData(array $data)
    {
        return $this->validateByDataFormat($data) && $this->validateByDataValues($data);
    }

    /**
     * @param $item
     * @param $key
     */
    protected function trim(&$item, $key)
    {
        $item = trim($item);
    }
}