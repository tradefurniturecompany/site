<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model;

/**
 * Declarations of core registry keys used by the CommonRules module
 *
 */
class RegistryConstants
{
    /**
     * Rule table names for modules
     */
    const SHIPPING_RULES_RULE_TABLE_NAME = 'amasty_shiprules_rule';
    const SHIPPING_RESTRICTIONS_RULE_TABLE_NAME = 'amasty_shiprestriction_rule';
    const PAYMENT_RESTRICTIONS_RULE_TABLE_NAME = 'am_payrestriction_rule';

    const AMASTY_SPECIAL_PROMOTIONS_PRO_MODULE_NAME = 'Amasty_RulesPro';
}