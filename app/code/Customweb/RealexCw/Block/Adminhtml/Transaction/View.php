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

namespace Customweb\RealexCw\Block\Adminhtml\Transaction;

class View extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Block group
	 *
	 * @var string
	 */
	protected $_blockGroup = 'Customweb_RealexCw';

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Widget\Context $context,
			\Magento\Framework\Registry $registry,
			array $data = []
	) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_objectId = 'entity_id';
		$this->_controller = 'adminhtml_transaction';
		$this->_mode = 'view';

		parent::_construct();

		$this->buttonList->remove('delete');
		$this->buttonList->remove('reset');
		$this->buttonList->remove('save');

		$this->setId('realexcw_transaction_view');
		$transaction = $this->getTransaction();

		if (!$transaction) {
			return;
		}

		
	}

	/**
	 * Retrieve transaction model object
	 *
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function getTransaction()
	{
		return $this->_coreRegistry->registry('realexcw_transaction');
	}

	/**
	 * Retrieve Transaction Identifier
	 *
	 * @return int
	 */
	public function getTransactionId()
	{
		return $this->getTransaction() ? $this->getTransaction()->getId() : null;
	}

	/**
	 * Get header text
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getHeaderText()
	{
		return __(
				'Transaction # %1 | %3',
				$this->getTransaction()->getIncrementId(),
				$this->formatDate(
						$this->_localeDate->date(new \DateTime($this->getTransaction()->getCreatedOn())),
						\IntlDateFormatter::MEDIUM,
						true
				)
		);
	}

	/**
	 * URL getter
	 *
	 * @param string $params
	 * @param array $params2
	 * @return string
	 */
	public function getUrl($params = '', $params2 = [])
	{
		$params2['transaction_id'] = $this->getTransactionId();
		return parent::getUrl($params, $params2);
	}

	/**
	 * Update URL getter
	 *
	 * @return string
	 */
	public function getUpdateUrl()
	{
		return $this->getUrl('realexcw/transaction/update');
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 * @return bool
	 */
	protected function _isAllowedAction($resourceId)
	{
		return $this->_authorization->isAllowed($resourceId);
	}

	/**
	 * Return back url for view grid
	 *
	 * @return string
	 */
	public function getBackUrl()
	{
		if ($this->getTransaction() && $this->getTransaction()->getBackUrl()) {
			return $this->getTransaction()->getBackUrl();
		}

		return $this->getUrl('realexcw/transaction/');
	}
}