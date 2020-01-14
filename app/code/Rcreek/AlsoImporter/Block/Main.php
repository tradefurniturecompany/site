<?php

namespace Rcreek\AlsoImporter\Block;
class Main extends \Magento\Framework\View\Element\Template
{
    public $skus;

    protected $_productRepository;
    protected $_action;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Action $action,
        array $data = []
    )
    {
        $this->_productRepository = $productRepository;
        $this->_action = $action;
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
        $file = fopen($directory->getRoot() . "/also_available.csv", "r");
        $cnt = 0;
        while ($line = fgetcsv($file, 0)) {
            if ($cnt > 0 && count($line) === 4) {
                $this->skus[] = $line;
            }
            $cnt++;
        }

        foreach ($this->skus as &$line) {
            try {
                $product = $this->getProductBySku($line[0]);

                if($line[1] !== 'NULL') {
                    $this->_action->updateAttributes([$product->getId()], ['also_available_light_sku' => $line[1]], 0);
                }

                if($line[2] !== 'NULL') {
                    $this->_action->updateAttributes([$product->getId()], ['also_available_dark_sku' => $line[2]], 0);
                }

                if($line[3] !== 'NULL') {
                    $this->_action->updateAttributes([$product->getId()], ['also_available_natural_sku' => $line[3]], 0);
                }

                $line[4] = 'ok';
            } catch (\Exception $e) {
                $line[4] = 'FAIL';
            }
        }

        fclose($file);
    }
}
