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

namespace Customweb\RealexCw\Controller\Adminhtml\Config;

class Save extends \Customweb\RealexCw\Controller\Adminhtml\Config
{
	public function execute()
	{
		$form = $this->_formStructure->getForm($this->getRequest()->getParam('form'));

		$this->updateStoreHierarchy();

		$this->storeConfigState();

		try {
			$formData = $this->getRequest()->getParams();
			$settingHandler = $this->_container->getBean('Customweb_Payment_SettingHandler');


			$this->_formStructure->getFormAdapter()->processForm($form, $this->getPressedButton($form), $formData);
			$this->messageManager->addSuccess(__('The configuration has been saved.'));
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
		}

		/* @var $resultRedirect \Magento\Backend\Model\View\Result\Redirect  */
		$resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect->setPath('*/*/', ['_current' => true]);
	}

	/**
	 * @return void
	 */
	private function storeConfigState()
	{
		/* @var $storage \Customweb_Storage_IBackend */
		$storage = $this->_container->getBean('Customweb_Storage_IBackend');
		if (!($storage instanceof \Customweb_Storage_IBackend)) {
			return;
		}

		$requestConfigState = $this->getRequest()->getParam('config_state');
		if (!is_array($requestConfigState)) {
			return;
		}

		$storedConfigState = $storage->read(\Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm::STORAGE_CONFIG_SPACE,
				\Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm::STORAGE_CONFIG_STATE_KEY);
		if (is_array($storedConfigState)) {
			$updatedConfigState = array_replace_recursive($storedConfigState, $requestConfigState);
		} else {
			$updatedConfigState = $requestConfigState;
		}

		$storage->write(\Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm::STORAGE_CONFIG_SPACE,
				\Customweb\RealexCw\Model\Renderer\Adminhtml\BackendForm::STORAGE_CONFIG_STATE_KEY, $updatedConfigState);
	}

	/**
	 * @param \Customweb_Payment_BackendOperation_IForm $form
	 * @return string
	 * @throws \Exception
	 */
	private function getPressedButton(\Customweb_Payment_BackendOperation_IForm $form)
	{
		$params = $this->getRequest()->getParams();
		if (!isset($params['button'])) {
			throw new \Exception(__('No button returned.'));
		}
		$pressedButton = null;
		foreach ($form->getButtons() as $button) {
			if ($button->getMachineName() == $params['button']) {
				$pressedButton = $button;
			}
		}
		if ($pressedButton === null) {
			throw new \Exception(__('Could not find pressed button.'));
		}
		return $pressedButton;
	}
}