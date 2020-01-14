<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model\Rate\Import;

use Amasty\ShippingTableRates\Helper\Data;
use Amasty\ShippingTableRates\Model\ResourceModel\Rate;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Import Rates from csv and assign them to shipping method
 */
class RateImportService
{
    const MAX_LINE_LENGTH = 50000;

    const COL_NUMS = 20;

    const HIDDEN_COLUMNS = 2;

    const BATCH_SIZE = 50000;

    const COUNTRY = 0;

    const STATE = 1;

    const CITY = 2;

    const ZIP_FROM = 3;

    const NUM_ZIP_FROM = 18;

    const ZIP_TO = 4;

    const NUM_ZIP_TO = 19;

    const PRICE_TO = 6;

    const WEIGHT_TO = 8;

    const QTY_TO = 10;

    const SHIPPING_TYPE = 11;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var File
     */
    private $file;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Rate
     */
    private $rateResource;

    public function __construct(
        File $file,
        Data $helper,
        Rate $rateResource
    ) {
        $this->file = $file;
        $this->helper = $helper;
        $this->rateResource = $rateResource;
    }

    /**
     * @param int $methodId
     * @param string $fileName
     *
     * @return array
     */
    public function import($methodId, $fileName)
    {
        $err = [];

        $fileResource = $this->file->fileOpen($fileName, 'r');
        $methodId = (int)$methodId;
        if (!$methodId) {
            $err[] = __('Specify a valid method ID.');

            return $err;
        }

        $countryCodes = $this->helper->getCountries();
        $countryNames = $this->helper->getCountries(true);
        $typeLabels = $this->helper->getTypes(true);

        $currLineNum = 0;

        while (($line = $this->file->fileGetCsv($fileResource, self::MAX_LINE_LENGTH, ',', '"')) !== false) {
            $currLineNum++;

            if ($currLineNum == 1) {
                continue;
            }

            if ((count($line) + self::HIDDEN_COLUMNS) != self::COL_NUMS) {
                $err[] = 'Line #' . $currLineNum . ': warning, expected number of columns is ' . self::COL_NUMS;
                $lineCount = count($line);
                if ($lineCount > self::COL_NUMS) {
                    for ($i = 0; $i < $lineCount - self::COL_NUMS; $i++) {
                        unset($line[self::COL_NUMS + $i]);
                    }
                }

                if ($lineCount < self::COL_NUMS) {
                    for ($i = 0; $i < self::COL_NUMS - $lineCount; $i++) {
                        $line[$lineCount + $i] = 0;
                    }
                }
            }

            $dataZipFrom = $this->helper->getDataFromZip($line[self::ZIP_FROM]);
            $dataZipTo = $this->helper->getDataFromZip($line[self::ZIP_TO]);
            $line[self::NUM_ZIP_FROM] = $dataZipFrom['district'];
            $line[self::NUM_ZIP_TO] = $dataZipTo['district'];
            $countries = [''];

            for ($i = 0; $i < self::COL_NUMS - self::HIDDEN_COLUMNS; $i++) {
                $line[$i] = str_replace(["\r", "\n", "\t", "\\", '"', "'", "*"], '', $line[$i]);
            }

            if ($line[self::COUNTRY]) {
                $countries = explode(',', htmlspecialchars_decode($line[self::COUNTRY]));
            } else {
                $line[self::COUNTRY] = '0';
            }

            $line = $this->_setDefaultLineValues($line);

            $typesData = $this->_prepareLineTypes($line, $err, $currLineNum, $typeLabels);

            $line = $typesData['line'];
            $err = $typesData['err'];

            foreach ($countries as $country) {
                if ($country == 'All') {
                    $country = 0;
                }

                if ($country && empty($countryCodes[$country])) {
                    if (in_array($country, $countryNames)) {
                        $countryCodes[$country] = array_search($country, $countryNames);
                    } else {
                        $err[] = 'Line #' . $currLineNum . ': invalid country code ' . $country;

                        continue;
                    }

                }
                $line[self::COUNTRY] = $country ? $countryCodes[$country] : '0';

                $statesData = $this->_prepareLineStates($line, $err, $currLineNum, $country, $methodId);
            }// countries
        } // end while read
        $this->file->fileClose($fileResource);

        if (isset($statesData['data_index'])) {
            $err = $this->returnErrors($statesData['data'], $methodId, $currLineNum, $statesData['err']);
        }

        return $err;
    }

    /**
     * @param array $line
     *
     * @return array
     */
    protected function _setDefaultLineValues($line)
    {
        if (!$line[self::PRICE_TO]) {
            $line[self::PRICE_TO] = \Amasty\ShippingTableRates\Model\Rate::MAX_VALUE;
        }
        if (!$line[self::WEIGHT_TO]) {
            $line[self::WEIGHT_TO] = \Amasty\ShippingTableRates\Model\Rate::MAX_VALUE;
        }
        if (!$line[self::QTY_TO]) {
            $line[self::QTY_TO] = \Amasty\ShippingTableRates\Model\Rate::MAX_VALUE;
        }

        return $line;
    }

    /**
     * @param array $line
     * @param array $err
     * @param int $currLineNum
     * @param array $typeLabels
     *
     * @return array
     */
    protected function _prepareLineTypes($line, $err, $currLineNum, $typeLabels)
    {
        $types = [''];

        if ($line[self::SHIPPING_TYPE]) {
            $types = explode(',', $line[self::SHIPPING_TYPE]);
        }

        foreach ($types as $type) {
            if ($type == 'All') {
                $type = 0;
            }
            if ($type && empty($typeLabels[$type])) {
                if (in_array($type, $typeLabels)) {
                    $typeLabels[$type] = array_search($type, $typeLabels);
                } else {
                    $err[] = 'Line #' . $currLineNum . ': invalid type code ' . $type;
                    continue;
                }

            }
            $line[self::SHIPPING_TYPE] = $type ? $typeLabels[$type] : '';
        }

        return ['line' => $line, 'err' => $err];
    }

    /**
     * @param array $line
     * @param array $err
     * @param int $currLineNum
     * @param int $country
     * @param int $methodId
     *
     * @return array
     */
    protected function _prepareLineStates($line, $err, $currLineNum, $country, $methodId)
    {
        $dataIndex = 0;
        $states = [''];
        $zips = [''];

        if ($line[self::STATE]) {
            $states = explode(',', $line[self::STATE]);
        }

        if ($line[self::ZIP_FROM]) {
            $zips = explode(',', $line[self::ZIP_FROM]);
        }
        $stateNames = $this->helper->getStates(true);
        $stateCodes = $this->helper->getStates();

        foreach ($states as $state) {

            if ($state == 'All') {
                $state = '';
            }

            if ($state && empty($stateCodes[$state][$country])) {
                if (in_array($state, $stateNames)) {
                    $stateCodes[$state][$country] = array_search($state, $stateNames);
                } else {
                    $err[] = 'Line #' . $currLineNum . ': invalid state code ' . $state;

                    continue;
                }

            }
            $line[self::STATE] = $state ? $stateCodes[$state][$country] : '';

            foreach ($zips as $zip) {
                $line[self::ZIP_FROM] = $zip;
                $data[$dataIndex] = $line;
                $dataIndex++;

                if ($dataIndex > self::BATCH_SIZE) {
                    $err = $this->returnErrors($data, $methodId, $currLineNum, $err);
                    $data = [];
                    $dataIndex = 0;
                }
            }
        }

        if (!empty($data)) {
            $this->_data = array_merge($this->_data, $data);
        }

        return ['line' => $line, 'err' => $err, 'data_index' => $dataIndex, 'data' => $this->_data];
    }

    /**
     * @param array $data
     * @param int $methodId
     * @param int $currLineNum
     * @param array $err
     *
     * @return array
     */
    public function returnErrors($data, $methodId, $currLineNum, $err)
    {
        $errText = $this->rateResource->batchInsert($methodId, $data);

        if ($errText) {
            foreach ($data as $key => $value) {
                $newData[$key] = array_slice($value, 0, 12);
                $oldData[$key] = array_slice($value, 12);
            }

            $newData = array_unique($newData, SORT_REGULAR);
            $checkedData = [];
            foreach ($newData as $key => $value) {
                //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $checkedData[] = array_merge($value, $oldData[$key]);
            }

            $errText = $this->rateResource->batchInsert($methodId, $checkedData);
            if ($errText) {
                $err[] = 'Line #' . $currLineNum . ': duplicated conditions before this line have been skipped';
            } else {
                $err[] = 'Your csv file has been automatically cleared of duplicates and successfully uploaded';
            }
        }

        return $err;
    }
}
