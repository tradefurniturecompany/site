<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup;

use Amasty\MegaMenu\Setup\Operation\AddLinkType;
use Amasty\MegaMenu\Setup\Operation\UpdateLink;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AddLinkType
     */
    private $addLinkType;

    /**
     * @var UpdateLink
     */
    private $updateLink;

    public function __construct(
        AddLinkType $addLinkType,
        UpdateLink $updateLink
    ) {
        $this->addLinkType = $addLinkType;
        $this->updateLink = $updateLink;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addLinkType->execute($setup);
            $this->updateLink->execute($setup);
        }

        $setup->endSetup();
    }
}

