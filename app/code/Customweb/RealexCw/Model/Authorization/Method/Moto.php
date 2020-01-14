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

namespace Customweb\RealexCw\Model\Authorization\Method;

class Moto extends AbstractMethod
{
	protected function getAdapterInterfaceName()
	{
		return 'Customweb_Payment_Authorization_Moto_IAdapter';
	}

	protected function getAliasFormField() {
		
		if ($this->getContext()->getOrderContext()->getPaymentMethod()->getPaymentMethodConfigurationValue('alias_manager') != 'active') {
			return null;
		}

		$aliasElement = null;
		$aliasTransactions = $this->_aliasHandler->getAliasTransactions($this->getContext()->getOrderContext());
		if (is_array($aliasTransactions) && !empty($aliasTransactions)) {
			$options = ['' => ''];
			foreach ($aliasTransactions as $transaction) {
				$options[$transaction->getId()] = $transaction->getAliasForDisplay();
			}
			$selectControl = new \Customweb_Form_Control_Select('alias', $options, $this->getContext()->getAliasTransaction());

			$aliasElement = new \Customweb_Form_Element(__('Stored payment details'), $selectControl, __('You may choose one of your stored payment details.'));
			$aliasElement->setRequired(false);
			$aliasElement->setElementIntention(new \Customweb_Form_Intention_Intention('alias'));
			return $aliasElement;
		}
		
	}

	public function getHiddenFormFields()
	{
		$result = $this->getAdapter()->getParameters($this->getContext()->getTransaction()->getTransactionObject());
		$this->getContext()->getTransaction()->save();
		return $result;
	}

	/**
	 * @return \Customweb_Payment_Authorization_Moto_IAdapter
	 */
	public function getAdapter()
	{
		return parent::getAdapter();
	}
}