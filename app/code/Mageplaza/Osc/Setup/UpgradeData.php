<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Setup;

use Exception;
use Magento\Cms\Model\BlockFactory;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreRepository;
use Mageplaza\Osc\Helper\Data as OscHelper;
use Psr\Log\LoggerInterface;
use Zend_Serializer_Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OscHelper
     */
    protected $oscHelper;

    /**
     * @var Config
     */
    protected $resourceConfig;

    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
     *
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     * @param OscHelper $oscHelper
     * @param Config $resourceConfig
     * @param StoreRepository $storeRepository
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        Filesystem $filesystem,
        LoggerInterface $logger,
        OscHelper $oscHelper,
        Config $resourceConfig,
        StoreRepository $storeRepository,
        BlockFactory $blockFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->fileSystem = $filesystem;
        $this->logger = $logger;
        $this->oscHelper = $oscHelper;
        $this->resourceConfig = $resourceConfig;
        $this->storeRepository = $storeRepository;
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $entityAttributesCodes = [
                'osc_gift_wrap_amount'      => Table::TYPE_DECIMAL,
                'base_osc_gift_wrap_amount' => Table::TYPE_DECIMAL
            ];
            foreach ($entityAttributesCodes as $code => $type) {
                $quoteInstaller->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
                $quoteInstaller->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('invoice', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('creditmemo', $code, ['type' => $type, 'visible' => false]);
            }

            $quoteInstaller->addAttribute('quote_address', 'used_gift_wrap', [
                'type'    => Table::TYPE_BOOLEAN,
                'visible' => false
            ]);
            $quoteInstaller->addAttribute('quote_address', 'gift_wrap_type', [
                'type'    => Table::TYPE_SMALLINT,
                'visible' => false
            ]);
            $salesInstaller->addAttribute('order', 'gift_wrap_type', [
                'type'    => Table::TYPE_SMALLINT,
                'visible' => false
            ]);
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $salesInstaller->addAttribute('order', 'osc_delivery_time', [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]);
        }

        if (version_compare($context->getVersion(), '2.1.2') < 0) {
            $salesInstaller->addAttribute('order', 'osc_survey_question', [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]);
            $salesInstaller->addAttribute('order', 'osc_survey_answers', [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]);
        }

        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $salesInstaller->addAttribute('order', 'osc_order_house_security_code', [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]);
        }

        if (version_compare($context->getVersion(), '2.1.4') < 0) {
            $this->insertBlock();
        }

        if (version_compare($context->getVersion(), '2.1.5') < 0) {
            $this->updateSealBlock($setup);
            $this->copyDefaultSeal();
        }

        $setup->endSetup();
    }

    /**
     * @return $this
     * @throws Exception
     */
    private function insertBlock()
    {
        $block = $this->getSealBlockData();

        $cmsBlock = $this->blockFactory->create()->load($block['identifier'], 'identifier');
        if (!$cmsBlock->getId()) {
            $cmsBlock->setData($block)
                ->save();
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getSealBlockData()
    {
        $sealContent = '
            <div class="osc-trust-seals" style="text-align: center;">
                <div class="trust-seals-badges">
                    <a href="https://en.wikipedia.org/wiki/Trust_seal" target="_blank">
                        <img src="{{view url=Mageplaza_Osc/css/images/seal.png}}">
                    </a>
                </div>
                <div class="trust-seals-text">
                    <p>This is a demonstration of trust badge. Please contact your SSL or Security provider to have trust badges embed properly</p>
                </div>
            </div>';

        return [
            'title'      => __('One Step Checkout Seal Content'),
            'identifier' => 'osc-seal-content',
            'content'    => $sealContent,
            'stores'     => [Store::DEFAULT_STORE_ID],
            'is_active'  => 1
        ];
    }

    /**
     * @param ModuleDataSetupInterface $setup
     *
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    private function updateSealBlock($setup)
    {
        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $storeId = $store->getId();
            if ($this->oscHelper->isEnableStaticBlock($storeId)) {
                continue;
            }

            $config = $this->oscHelper->getStaticBlockList($storeId);
            if ($config && is_array($config)) {
                foreach ($config as $key => $row) {
                    if ($row['position'] == 4) {
                        if (!isset($blockId)) {
                            $blockId = $row['block'];
                        }
                        unset($config[$key]);
                    }
                }

                $data = [
                    'osc/display_configuration/seal_block/is_enabled_seal_block' => 1,
                    'osc/block_configuration/list'                               => $this->oscHelper->serialize($config)
                ];
                if (isset($blockId)) {
                    $data['osc/display_configuration/seal_block/seal_static_block'] = $blockId;
                }
                $this->saveConfig($setup, $data, $storeId);
            }
        }
    }

    /**
     * Save config value
     *
     * @param ModuleDataSetupInterface $setup
     * @param array $data
     * @param int $scopeId
     *
     * @return $this
     * @throws LocalizedException
     */
    private function saveConfig($setup, $data, $scopeId)
    {
        $scope = $scopeId ? 'stores' : 'default';

        $connection = $setup->getConnection();
        foreach ($data as $path => $value) {
            $select = $connection->select()->from(
                $this->resourceConfig->getMainTable()
            )
                ->where('path = ?', $path)
                ->where('scope = ?', $scope)
                ->where('scope_id = ?', $scopeId);

            $row = $connection->fetchRow($select);

            $newData = ['scope' => $scope, 'scope_id' => $scopeId, 'path' => $path, 'value' => $value];
            if ($row) {
                $whereCondition = [$this->resourceConfig->getIdFieldName() . '=?' => $row[$this->resourceConfig->getIdFieldName()]];
                $connection->update($this->resourceConfig->getMainTable(), $newData, $whereCondition);
            } elseif ($scope == 'default') {
                $connection->insert($this->resourceConfig->getMainTable(), $newData);
            }
        }

        return $this;
    }

    /**
     * Copy default seal images
     */
    private function copyDefaultSeal()
    {
        try {
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

            $mediaDirectory->create('mageplaza/osc/seal/default');
            $targetPath = $mediaDirectory->getAbsolutePath('mageplaza/osc/seal/default/seal.png');

            $DS = DIRECTORY_SEPARATOR;
            $oriPath = dirname(__DIR__) . $DS . 'view' . $DS . 'base' . $DS . 'web' . $DS . 'css' . $DS . 'images' . $DS . 'seal.png';

            $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
