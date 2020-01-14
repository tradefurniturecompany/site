<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->addCouponFields($setup, 'coupon_disable', 'Coupon Disable');
            $this->addCouponFields($setup, 'discount_id_disable', 'Disable Discount ID');
            $this->addCouponFields($setup, 'coupon', 'Coupon');
            $this->addCouponFields($setup, 'discount_id', 'Discount ID');
        }

        $setup->endSetup();
    }

    protected function addCouponFields(SchemaSetupInterface $setup, $nameOfField, $comment = '')
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('am_payrestriction_rule'),
            $nameOfField,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => $comment
            ]
        );
    }
}