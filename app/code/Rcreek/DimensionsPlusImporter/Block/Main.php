<?php

namespace Rcreek\DimensionsPlusImporter\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Eav\Model\Config;

class Main extends \Magento\Framework\View\Element\Template
{
    public $skus;
    public $errorMessage;

    protected $_productRepository;
    protected $_action;
    protected $_eav;

    protected $_lengthRangeOptions = [];
    protected $_widthRangeOptions  = [];
    protected $_heightRangeOptions = [];
    protected $_materialOptions    = [];
    protected $_finishOptions      = [];

    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Action $action,
        Config $eav,
        array $data = []
    )
    {
        $this->_productRepository = $productRepository;
        $this->_action = $action;
        $this->_eav = $eav;

        // get the options for dropdown attributes
        $options = $this->_eav->getAttribute('catalog_product', 'length_range')->getSource()->getAllOptions();
        foreach ($options as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $this->_lengthRangeOptions[trim($value['label'])] = $value['value'];
        }

        // get the options for dropdown attributes
        $options = $this->_eav->getAttribute('catalog_product', 'width_range')->getSource()->getAllOptions();
        foreach ($options as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $this->_widthRangeOptions[trim($value['label'])] = $value['value'];
        }

        // get the options for dropdown attributes
        $options = $this->_eav->getAttribute('catalog_product', 'height_range')->getSource()->getAllOptions();
        foreach ($options as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $this->_heightRangeOptions[trim($value['label'])] = $value['value'];
        }

        // get the options for dropdown attributes
        $options = $this->_eav->getAttribute('catalog_product', 'material')->getSource()->getAllOptions();
        foreach ($options as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $this->_materialOptions[trim($value['label'])] = $value['value'];
        }

        // get the options for dropdown attributes
        $options = $this->_eav->getAttribute('catalog_product', 'finish')->getSource()->getAllOptions();
        foreach ($options as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $this->_finishOptions[trim($value['label'])] = $value['value'];
        }

        parent::__construct($context, $data);
    }

    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    function _prepareLayout()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

        if (!is_file($directory->getRoot() . "/var/import/unprepared_dimensions_plus.csv")) {
            $this->errorMessage = 'cannot find file: ' . $directory->getRoot() . "/var/import/unprepared_dimensions_plus.csv";

            return;
        }

        try {
            $file = fopen($directory->getRoot() . "/var/import/unprepared_dimensions_plus.csv", "r");
        } catch (\Exception $e) {
            $this->errorMessage = 'cannot read file: ' . $directory->getRoot() . "/var/import/unprepared_dimensions_plus.csv";

            return;
        }

        $cnt = 0;
        while ($line = fgetcsv($file, 0)) {
            if ($cnt === 0) {
                if ($line[0] !== 'sku') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the first column must be "sku"';

                    return;
                }

                if ($line[1] !== 'height_in_cm') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the second column must be "height_in_cm"';

                    return;
                }

                if ($line[2] !== 'width_in_cm') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the third column must be "width_in_cm"';

                    return;
                }

                if ($line[3] !== 'length_in_cm') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the forth column must be "length_in_cm"';

                    return;
                }

                if ($line[4] !== 'materials') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the fifth column must be "materials"';

                    return;
                }

                if ($line[5] !== 'finishes') {
                    $this->errorMessage = 'unprepared_dimensions_plus.csv must have a header row, and the sixth column must be "finishes"';

                    return;
                }
            }

            if ($cnt > 0 && count($line) === 6) {
                $this->skus[] = $line;
            } elseif ($cnt > 0) {
                $this->errorMessage = 'unprepared_dimensions_plus.csv must have only 6 columns not ' . count($line);

                return;
            }
            $cnt++;
        }

        if (!$this->skus) {
            $this->errorMessage = 'no skus found';

            return;
        }

        foreach ($this->skus as &$line) {
            try {
                $product = $this->getProductBySku($line[0]);

                $line[6] = 'NULL';
                $line[7] = 'NULL';
                $line[8] = 'NULL';
                $line[9] = 'NULL';
                $line[10] = 'NULL';
                $line[11] = 'NULL';
                $line[12] = 'NULL';
                $line[13] = 'NULL';
                $line[14] = 'NULL';
                $line[15] = 'NULL';
                $line[16] = 'NULL';

                if ($line[1] !== 'NULL' && $line[1] != 0) {
                    $this->_action->updateAttributes([$product->getId()], ['c2c_height' => $line[1]], 0);

                    $line[6] = $this->getHeightRange($line[1]);
                    $line[7] = $this->getHeightRangeId($line[6]);
                    if (isset($line[7]) && is_numeric($line[7])) {
                        $this->_action->updateAttributes([$product->getId()], ['height_range' => $line[7]], 0);
                    }
                }

                if ($line[2] !== 'NULL' && $line[2] != 0) {
                    $this->_action->updateAttributes([$product->getId()], ['c2c_width' => $line[2]], 0);

                    $line[8] = $this->getWidthRange($line[2]);
                    $line[9] = $this->getWidthRangeId($line[8]);
                    if (isset($line[9]) && is_numeric($line[9])) {
                        $this->_action->updateAttributes([$product->getId()], ['width_range' => $line[9]], 0);
                    }
                }

                if ($line[3] !== 'NULL' && $line[3] != 0) {
                    $this->_action->updateAttributes([$product->getId()], ['c2c_length' => $line[3]], 0);

                    $line[10] = $this->getLengthRange($line[3]);
                    $line[11] = $this->getLengthRangeId($line[10]);
                    if (isset($line[11]) && is_numeric($line[11])) {
                        $this->_action->updateAttributes([$product->getId()], ['length_range' => $line[11]], 0);
                    }
                }

                if ($line[4] !== 'NULL' && $line[4] != '') {
                    $line[12] = $this->getMaterial($line[4]);
                    $line[13] = $this->getMaterialId($line[12]);
                    if (isset($line[13]) && is_numeric($line[13])) {
                        $this->_action->updateAttributes([$product->getId()], ['material' => $line[13]], 0);
                    }
                }

                if ($line[5] !== 'NULL' && $line[5] != '') {
                    $line[14] = $this->getFinish($line[5]);
                    $line[15] = $this->getFinishId($line[14]);
                    if (isset($line[15]) && is_numeric($line[15])) {
                        $this->_action->updateAttributes([$product->getId()], ['finish' => $line[15]], 0);
                    }
                }

                $line[16] = 'ok';
            } catch (\Exception $e) {
                $line[16] = 'FAIL ' . $e->getMessage();
            }
        }

        fclose($file);
    }

    private function getWidthRange($width)
    {
        switch (true) {
            case empty($width):
                return "";
                break;
            case $width <= 50:
                return "Under 50cm";
                break;
            case $width <= 100:
                return "50-100cm";
                break;
            case $width <= 150:
                return "100-150cm";
                break;
            case $width <= 200:
                return "150-200cm";
                break;
            case $width <= 250:
                return "200-250cm";
                break;
            default:
                return "Over 250cm";
                break;
        }
    }

    private function getHeightRangeId($heightRange)
    {
        if (!isset($this->_heightRangeOptions[$heightRange])) {
            return null;
        }

        return $this->_heightRangeOptions[$heightRange];
    }

    private function getWidthRangeId($widthRange)
    {
        if (!isset($this->_widthRangeOptions[$widthRange])) {
            return null;
        }

        return $this->_widthRangeOptions[$widthRange];
    }

    private function getLengthRangeId($lengthRange)
    {
        if (!isset($this->_lengthRangeOptions[$lengthRange])) {
            return null;
        }

        return $this->_lengthRangeOptions[$lengthRange];
    }

    private function getMaterialId($material)
    {
        if (!isset($this->_materialOptions[$material])) {
            return null;
        }

        return $this->_materialOptions[$material];
    }

    private function getFinishId($finish)
    {
        if (!isset($this->_finishOptions[$finish])) {
            return null;
        }

        return $this->_finishOptions[$finish];
    }


    private function getHeightRange($height)
    {
        switch (true) {
            case empty($height):
                return "";
                break;
            case $height <= 50:
                return "Under 50cm";
                break;
            case $height <= 100:
                return "50-100cm";
                break;
            case $height <= 150:
                return "100-150cm";
                break;
            case $height <= 200:
                return "150-200cm";
                break;
            default:
                return "Over 200cm";
                break;
        }
    }

    private function getLengthRange($length)
    {
        switch (true) {
            case empty($length):
                return "";
                break;
            case $length <= 50:
                return "Under 50cm";
                break;
            case $length <= 100:
                return "50-100cm";
                break;
            case $length <= 150:
                return "100-150cm";
                break;
            case $length <= 200:
                return "150-200cm";
                break;
            default:
                return "Over 200cm";
                break;
        }
    }

    private function getMaterial($material)
    {
        if (stristr(strtolower($material), 'sheesham') != false) {
            return 'Sheesham';
        }

        if (stristr(strtolower($material), 'mango') != false) {
            return 'Mango';
        }

        return "";
    }

    private function getFinish($finish)
    {
        if (stristr(strtolower($finish), 'dark') != false) {
            return 'Honey';
        }

        if (stristr(strtolower($finish), 'honey') != false) {
            return 'Honey';
        }

        if (stristr(strtolower($finish), 'light') != false) {
            return 'Light';
        }

        if (stristr(strtolower($finish), 'natural') != false) {
            return 'Natural';
        }

        return "";
    }

}
