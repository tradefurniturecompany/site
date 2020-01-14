<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Block\ExternalCheckout;

class Totals extends AbstractExternalCheckout
{
	/**
	 * @var \Magento\Sales\Model\Config
	 */
	protected $_salesConfig;

	/**
	 * @var LayoutProcessorInterface[]
	 */
	protected $layoutProcessors;

	/**
	 * @var array
	 */
	protected $_totals = null;

	/**
	 * @var array
	 */
	protected $_totalRenderers;

	/**
	 * @var string
	 */
	protected $_defaultRenderer = 'Magento\Checkout\Block\Total\DefaultTotal';

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Sales\Model\Config $salesConfig
	 * @param array $layoutProcessors
	 * @param array $data
	 * @codeCoverageIgnore
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Sales\Model\Config $salesConfig,
			array $layoutProcessors = [],
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_salesConfig = $salesConfig;
		$this->_isScopePrivate = true;
		$this->layoutProcessors = $layoutProcessors;
	}

	/**
	 * @return array
	 */
	public function getTotals()
	{
		return $this->getTotalsCache();
	}

	/**
	 * @return array
	 */
	public function getTotalsCache()
	{
		if (empty($this->_totals)) {
			if ($this->getQuote()->isVirtual()) {
				$this->_totals = $this->getQuote()->getBillingAddress()->getTotals();
			} else {
				$this->_totals = $this->getQuote()->getShippingAddress()->getTotals();
			}
		}
		return $this->_totals;
	}

	/**
	 * @param array $value
	 * @return $this
	 * @codeCoverageIgnore
	 */
	public function setTotals($value)
	{
		$this->_totals = $value;
		return $this;
	}

	/**
	 * @param string $code
	 * @return BlockInterface
	 */
	protected function _getTotalRenderer($code)
	{
		$blockName = $code . '_total_renderer';
		$block = $this->getLayout()->getBlock($blockName);
		if (!$block) {
			$renderer = $this->_salesConfig->getTotalsRenderer('quote', 'totals', $code);
			if (!empty($renderer)) {
				$block = $renderer;
			} else {
				$block = $this->_defaultRenderer;
			}

			$block = $this->getLayout()->createBlock($block, $blockName);
		}
		/**
		 * Transfer totals to renderer
		 */
		$block->setTotals($this->getTotals());
		return $block;
	}

	/**
	 * @param mixed $total
	 * @param int|null $area
	 * @param int $colspan
	 * @return string
	 */
	public function renderTotal($total, $area = null, $colspan = 1)
	{
		$code = $total->getCode();
		if ($total->getAs()) {
			$code = $total->getAs();
		}
		if ($code == 'grand_total') {
			$total->setTitle(__('Order Total'));
		}
		return $this->_getTotalRenderer(
				$code
		)->setTotal(
				$total
		)->setColspan(
				$colspan
		)->setRenderingArea(
				$area === null ? -1 : $area
		)->toHtml();
	}

	/**
	 * Render totals html for specific totals area (footer, body)
	 *
	 * @param   null|string $area
	 * @param   int $colspan
	 * @return  string
	 */
	public function renderTotals($area = null, $colspan = 1)
	{
		$html = '';
		foreach ($this->getTotals() as $total) {
			if ($total->getArea() != $area && $area != -1) {
				continue;
			}
			$html .= $this->renderTotal($total, $area, $colspan);
		}
		return $html;
	}

	/**
	 * Check if we have display grand total in base currency
	 *
	 * @return bool
	 */
	public function needDisplayBaseGrandtotal()
	{
		$quote = $this->getQuote();
		if ($quote->getBaseCurrencyCode() != $quote->getQuoteCurrencyCode()) {
			return true;
		}
		return false;
	}

	/**
	 * Get formated in base currency base grand total value
	 *
	 * @return string
	 */
	public function displayBaseGrandtotal()
	{
		$firstTotal = reset($this->_totals);
		if ($firstTotal) {
			$total = $firstTotal->getAddress()->getBaseGrandTotal();
			return $this->_storeManager->getStore()->getBaseCurrency()->format($total, [], true);
		}
		return '-';
	}

	/**
	 * @return \Magento\Quote\Model\Quote
	 */
	public function getQuote()
	{
		return $this->getContext()->getQuote();
	}
}