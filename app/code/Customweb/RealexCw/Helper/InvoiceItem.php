<?php
namespace Customweb\RealexCw\Helper;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Creditmemo\Item as CI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item as II;
class InvoiceItem extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magento\Tax\Model\Calculation
	 */
	protected $_taxCalculation;

	/**
	 * @var \Magento\Tax\Helper\Data
	 */
	protected $_taxHelper;

	/**
	 * @var \Magento\Weee\Helper\Data
	 */
	protected $_weeeHelper;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Tax\Model\Calculation $taxCalculation
	 * @param \Magento\Tax\Helper\Data $taxHelper
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Tax\Model\Calculation $taxCalculation,
			\Magento\Tax\Helper\Data $taxHelper,
			\Magento\Weee\Helper\Data $weeeHelper
	) {
		parent::__construct($context);
		$this->_taxCalculation = $taxCalculation;
		$this->_taxHelper = $taxHelper;
		$this->_weeeHelper = $weeeHelper;
	}

	/**
	 * 2020-03-13 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * @used-by \Customweb\RealexCw\Model\Authorization\OrderContext::assembleDataFromOrder()
	 * @used-by \Customweb\RealexCw\Model\Authorization\OrderContext::assembleDataFromQuote()
	 * @used-by \Customweb\RealexCw\Model\ExternalCheckout\Context::updateFromQuote()
	 * @used-by \Customweb\RealexCw\Model\Payment\Method\AbstractMethod::capture()
	 * @used-by \Customweb\RealexCw\Model\Payment\Method\AbstractMethod::refund()
	 * @param CI[]|II[]|OI[]|QI[] $items
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $billingAddress
	 * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $shippingAddress
	 * @param \Magento\Store\Model\Store $store
	 * @param double $discountAmount
	 * @param double $discountTaxAmount
	 * @param string $discountDescription
	 * @param double $shippingAmount
	 * @param double $shippingTaxAmount
	 * @param string $shippingDescription
	 * @param int $customerId
	 * @param double $grandTotal
	 * @param boolean $useBaseCurrency
	 * @param float $foomanSurchargeAmount
	 * @param boolean $adjust
	 * @param \Customweb_Payment_Authorization_IInvoiceItem[] $baseInvoiceItems
	 * @return \Customweb_Payment_Authorization_IInvoiceItem[]
	 */
	public function getInvoiceItems(
			array $items,
			$billingAddress,
			$shippingAddress,
			\Magento\Store\Model\Store $store,
			$discountAmount,
			$discountTaxAmount,
			$discountDescription,
			$shippingAmount,
			$shippingTaxAmount,
			$shippingDescription,
			$customerId,
			$grandTotal,
			$useBaseCurrency,
			$foomanSurchargeAmount = 0,
			$adjust = true,
			array $baseInvoiceItems = []
	) {
		$invoiceItems = [];

		foreach ($items as $item) { /** @var CI|II|OI|QI $item */
			$parentItem = null;
			if ($item->getOrderItem() != null && $item->getOrderItem()->getParentItemId() != null) {
				$parentItem = $item->getOrderItem()->getParentItem();
			} elseif ($item->getParentItemId() != null) {
				$parentItem = $item->getParentItem();
			}
			if (($parentItem != null && $parentItem->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
			|| ($parentItem == null && $item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
			) {
				continue;
			}

			$orderItemId = $item->getOrderItem() != null ? $item->getOrderItem()->getId() : $item->getId();

			$invoiceItem = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
					(string)$item->getSku().'_'.$orderItemId,
					(string)$item->getName(),
					(double)$item->getTaxPercent(),
					(double)($useBaseCurrency ? $item->getBaseRowTotalInclTax() : $item->getRowTotalInclTax()),
					(double)($item->getQty() ? $item->getQty() : $item->getQtyOrdered()),
					\Customweb_Payment_Authorization_IInvoiceItem::TYPE_PRODUCT,
					(string)$item->getSku(),
					$item->getProductType() != \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL
			);
			$invoiceItems[] = $invoiceItem;
		}

		if ($discountAmount < 0) {
			// 2020-03-13 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Division by zero in app/code/Customweb/RealexCw/Helper/InvoiceItem.php on line 129»:
			// https://github.com/tradefurniturecompany/site/issues/132
			if (dff_eq0(abs($discountAmount) - abs($discountTaxAmount))) {
				$item = df_first($items); /** @var CI|II|OI|QI $item */
				df_log_l($this, [
					'discountAmount' => $discountAmount
					,'discountTaxAmount' => $discountTaxAmount
					,'itemClass' => get_class(df_first($items))
					,'The sales document ID' => df_sales_doc($item)->getId()

				], 'division-by-zero');
			}
			$discountTaxRate = abs($discountTaxAmount) / (abs($discountAmount) - abs($discountTaxAmount)) * 100;
			$discountItem = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
					'discount',
					(string)__('Discount'),
					(double)($this->_taxHelper->applyTaxAfterDiscount($store) ? $discountTaxRate : 0),
					(double)abs($discountAmount),
					1,
					\Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT
			);
			$invoiceItems[] = $discountItem;
		}

		if ($shippingAmount > 0) {
			$baseShippingInvoiceItem = $this->getShippingInvoiceItem($baseInvoiceItems);
			if ($baseShippingInvoiceItem !== false) {
				$shippingTaxRate = $baseShippingInvoiceItem->getTaxRate();
			} else {
				$shippingTaxClassId = $this->_taxHelper->getShippingTaxClass($store);
				$shippingTaxRequest = $this->_taxCalculation->getRateRequest($shippingAddress, $billingAddress, null, $store, $customerId);
				$shippingTaxRequest->setProductClassId($shippingTaxClassId);
				$shippingTaxRate = $this->_taxCalculation->getRate($shippingTaxRequest);
			}

			$shippingItem = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
					'shipping',
					(string)$shippingDescription,
					(double)$shippingTaxRate,
					(double)$shippingAmount,
					1,
					\Customweb_Payment_Authorization_IInvoiceItem::TYPE_SHIPPING
			);
			$invoiceItems[] = $shippingItem;
		}

		if ($foomanSurchargeAmount > 0) {
			$foomanSurcharge = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
					'fooman_surcharge',
					(string)__('Payment Surcharge'),
					0,
					(double)$foomanSurchargeAmount,
					1,
					\Customweb_Payment_Authorization_IInvoiceItem::TYPE_FEE
			);
			$invoiceItems[] = $foomanSurcharge;
		}

		$weeeTotal = $this->_weeeHelper->getTotalAmounts($items);
		if ($weeeTotal > 0) {
			$weeeTax = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
					'weee_tax',
					(string)__('FPT'),
					0,
					(double) $weeeTotal,
					1,
					\Customweb_Payment_Authorization_IInvoiceItem::TYPE_FEE
			);
			$invoiceItems[] = $weeeTax;
		}

		if ($adjust) {
			$invoiceItemTotalAmount = \Customweb_Util_Invoice::getTotalAmountIncludingTax($invoiceItems);
			$compareAmounts = \Customweb_Util_Currency::compareAmount(
					$invoiceItemTotalAmount,
					$grandTotal,
					$useBaseCurrency ? $store->getBaseCurrencyCode() : $store->getCurrentCurrencyCode());
			if ($compareAmounts > 0) {
				$invoiceItems[] = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
						'adjustment_discount',
						(string)__('Adjustment Discount'),
						0,
						(double)($invoiceItemTotalAmount - $grandTotal),
						1,
						\Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT
				);
			} elseif ($compareAmounts < 0) {
				$invoiceItems[] = new \Customweb_Payment_Authorization_DefaultInvoiceItem(
						'adjustment_fee',
						(string)__('Adjustment Fee'),
						0,
						(double)($grandTotal - $invoiceItemTotalAmount),
						1,
						\Customweb_Payment_Authorization_IInvoiceItem::TYPE_FEE
				);
			}
		}

		return \Customweb_Util_Invoice::ensureUniqueSku($invoiceItems);
	}

	/**
	 * @param \Customweb_Payment_Authorization_IInvoiceItem[] $invoiceItems
	 * @return \Customweb_Payment_Authorization_IInvoiceItem
	 */
	private function getShippingInvoiceItem(array $invoiceItems)
	{
		foreach ($invoiceItems as $invoiceItem) {
			if ($invoiceItem->getType() == \Customweb_Payment_Authorization_IInvoiceItem::TYPE_SHIPPING) {
				return $invoiceItem;
			}
		}
		return false;
	}
}