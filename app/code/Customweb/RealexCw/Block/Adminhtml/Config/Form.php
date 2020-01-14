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

namespace Customweb\RealexCw\Block\Adminhtml\Config;

class Form extends \Magento\Backend\Block\Widget
{
	/**
	 * Block template File
	 *
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::realexcw/config/form.phtml';

	/**
	 * @var \Customweb\RealexCw\Model\Config\Structure
	 */
	protected $_formStructure;

	/**
	 * @var \Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm
	 */
	protected $_backendFormRenderer;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Customweb\RealexCw\Model\Config\Structure $formStructure,
	 * @param \Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm $backendFormRenderer
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Customweb\RealexCw\Model\Config\Structure $formStructure,
			\Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm $backendFormRenderer,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_formStructure = $formStructure;
		$this->_backendFormRenderer = $backendFormRenderer;
	}

	/**
	 * @return \Customweb_Form_IRenderer
	 */
	public function getFormRenderer() {
		return $this->_backendFormRenderer;
	}

	/**
	 * @return \Customweb_Payment_BackendOperation_IForm
	 * @throws \Exception
	 */
	public function getForm()
	{
		$form = $this->_formStructure->getForm($this->getRequest()->getParam('form'));
		$form = new \Customweb_Payment_BackendOperation_Form($form);
		if ($form->isProcessable()) {
			$form->setTargetUrl($this->_urlBuilder->getUrl('*/*/save', ['form' => $form->getMachineName(), '_current' => true]));
			$form->setRequestMethod(\Customweb_IForm::REQUEST_METHOD_POST);
		}
		return $form;
	}
}
