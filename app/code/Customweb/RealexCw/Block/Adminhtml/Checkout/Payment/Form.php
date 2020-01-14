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

namespace Customweb\RealexCw\Block\Adminhtml\Checkout\Payment;

class Form extends \Magento\Backend\Block\Widget
{
	/**
	 * @var \Customweb\RealexCw\Model\Renderer\AdminhtmlForm
	 */
	protected $_formRenderer;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod
	 */
	private $authorizationMethodAdapter;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private $transaction;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Customweb\RealexCw\Model\Renderer\Adminhtml\CheckoutForm $formRenderer
	 * @param \Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Customweb\RealexCw\Model\Renderer\Adminhtml\CheckoutForm $formRenderer,
			\Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_formRenderer = $formRenderer;
		$this->_authorizationMethodFactory = $authorizationMethodFactory;
	}

	/**
	 * Get cancel url
	 *
	 * @return string
	 */
	public function getCancelUrl()
	{
		return $this->getUrl('sales/order/cancel', ['order_id' => $this->getTransaction()->getOrderId()]);
	}

	/**
	 * @return \Customweb_Form_IRenderer
	 */
	public function getFormRenderer()
	{
		return $this->_formRenderer;
	}

	/**
	 * @return \Customweb_Form
	 */
	public function getForm()
	{
		$form = new \Customweb_Form();
		$form->setMachineName('payment_form');
		foreach ($this->getAuthorizationMethodAdapter()->getVisibleFormFields() as $formElement) {
			$form->addElement($formElement);
		}
		foreach ($this->getAuthorizationMethodAdapter()->getHiddenFormFields() as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $val) {
					$form->addElement(new \Customweb_Form_HiddenElement(new \Customweb_Form_Control_HiddenInput(\Customweb_Util_Html::escapeXml($key). '[]', \Customweb_Util_Html::escapeXml($val))));
				}
			} else {
				$form->addElement(new \Customweb_Form_HiddenElement(new \Customweb_Form_Control_HiddenInput(\Customweb_Util_Html::escapeXml($key), \Customweb_Util_Html::escapeXml($value))));
			}
		}
		$form->setTargetUrl($this->getAuthorizationMethodAdapter()->getFormActionUrl());
		return $form;
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod
	 */
	private function getAuthorizationMethodAdapter()
	{
		if (!($this->authorizationMethodAdapter instanceof \Customweb\RealexCw\Model\Authorization\Method\AbstractMethod)) {
			$context = $this->_authorizationMethodFactory->getContextFactory()->createTransaction($this->getTransaction());
			$this->authorizationMethodAdapter = $this->_authorizationMethodFactory->createMoto($context);
		}
		return $this->authorizationMethodAdapter;
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function getTransaction()
	{
		if (!($this->transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction)) {
			$this->transaction = $this->getParentBlock()->getTransaction();
		}
		return $this->transaction;
	}

	/**
	 * @param \Customweb\RealexCw\Model\Authorization\Transaction $transaction
	 * @return \Customweb\RealexCw\Block\Adminhtml\Checkout\Payment\Form
	 */
	public function setTransaction(\Customweb\RealexCw\Model\Authorization\Transaction $transaction)
	{
		$this->transaction = $transaction;
		return $this;
	}
}
