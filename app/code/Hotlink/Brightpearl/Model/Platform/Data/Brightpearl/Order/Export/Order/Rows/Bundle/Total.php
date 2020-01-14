<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Bundle;

class Total extends \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order\Rows\Simple\Total
{
    protected function _getRowTotal( \Magento\Sales\Model\Order\Item $item, $base, $useHiddenTax )
    {
        // Calculate total discount amount as the sum of children [base_]discount_amount
        //
        $childrenDiscountAmount  = 0;
        $childrenHiddenTaxAmount = 0;

        $children = $item->getChildrenItems();
        if (is_array($children)) {
            foreach ($children as $child) {
                $childrenDiscountAmount += ( $base )
                    ? $child->getBaseDiscountAmount()
                    : $child->getDiscountAmount();

                $childrenHiddenTaxAmount += ( $base )
                    ? $child->getBaseHiddenTaxAmount()
                    : $child->getHiddenTaxAmount();
            }
        }

        return ( $base )
            ?
            $item->getBaseRowTotal()
            + $item->getBaseTaxAmount()
            + $childrenHiddenTaxAmount /* + $item->getBaseHiddenTaxAmount() */
            + $item->getBaseWeeeTaxAppliedRowAmount()
            - ($item->getBaseDiscountAmount() + $childrenDiscountAmount)

            :
            $item->getRowTotal()
            + $item->getTaxAmount()
            + $childrenHiddenTaxAmount /* + $item->getHiddenTaxAmount() */
            + $item->getWeeeTaxAppliedRowAmount()
            - ($item->getDiscountAmount() + $childrenDiscountAmount);
    }

    protected function _getRowTax( \Magento\Sales\Model\Order\Item $item, $base, $useHiddenTax )
    {
        //
        // Bundle parent holds the total [base_]tax_amount but [base_]hidden_tax is spread on children
        //

        $childrenHiddenTaxAmount     = 0;
        $childrenBaseHiddenTaxAmount = 0;

        $children = $item->getChildrenItems();
        if (is_array($children) && $useHiddenTax) {
            foreach ($children as $child) {
                $childrenBaseHiddenTaxAmount += $child->getBaseHiddenTaxAmount();
                $childrenHiddenTaxAmount     += $child->getHiddenTaxAmount();
            }
        }

        return ( $base )
            ?
            $item->getBaseTaxAmount()
            + $item->getBaseWeeeTaxAppliedRowAmount()
            + $childrenBaseHiddenTaxAmount

            :
            $item->getTaxAmount()
            + $item->getWeeeTaxAppliedRowAmount()
            + $childrenHiddenTaxAmount;
    }
}