<?php
namespace MageKey\AdcPopup\Observer;
use MageKey\AdcPopup\Helper\Data as H;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item as I;
class QuoteAddAfter implements ObserverInterface {
	/**
	 * @param Observer $o
	 * @return void
	 */
	public function execute(Observer $o) {
		/**
		 * 2020-03-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * 1) «Registry key "mgk_adcpopup_items" already exists at vendor/magento/framework/Registry.php:60»:
		 * https://github.com/tradefurniturecompany/site/issues/130
		 * 2) If a customer reorder his previous order with multple items,
		 * then Magento adds these product to the cart one-by-one, so we go here multiple times.
		 * 2.1) @see \Magento\Sales\Controller\AbstractController\Reorder::execute():
		 *		foreach ($items as $item) {
		 *			try {
		 *				$cart->addOrderItem($item);
		 *			}
		 *			...
		 *		}
		 * https://github.com/magento/magento2/blob/2.3.2/app/code/Magento/Sales/Controller/AbstractController/Reorder.php#L63-L66
		 * 2.2) @see \Magento\Quote\Model\Quote::addProduct():
		 * 		$this->_eventManager->dispatch('sales_quote_product_add_after', ['items' => $items]);
		 * https://github.com/magento/magento2/blob/2.3.2/app/code/Magento/Quote/Model/Quote.php#L1682
		 */
		$ii = $o['items']; /** @var I[] $ii */
		if ($prev = df_registry($k = H::REGISTER_ITEMS_KEY)) { /** @var string $k */ /** @var I[]|null $prev */
			df_unregister($k);
			$ii = array_merge($ii, $prev);
		}
		df_register($k, $ii);
	}
}
