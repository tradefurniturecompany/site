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

class PaymentPage extends AbstractMethod
{
	protected function getAdapterInterfaceName()
	{
		return 'Customweb_Payment_Authorization_PaymentPage_IAdapter';
	}

	/**
	 * @return boolean
	 */
	public function isHeaderRedirectionSupported()
	{
		$result = $this->getAdapter()->isHeaderRedirectionSupported($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return $result;
	}

	public function getFormActionUrl()
	{
		$result = $this->getAdapter()->getFormActionUrl($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return (string) $result;
	}

	/**
	 * @return string
	 */
	public function getRedirectionUrl()
	{
		$result = $this->getAdapter()->getRedirectionUrl($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return (string) $result;
	}

	public function getHiddenFormFields()
	{
		$result = $this->getAdapter()->getParameters($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return $result;
	}

	/**
	 * @return array
	 */
	public function startAuthorization()
	{
		$data = new \Customweb\RealexCw\Model\Service\AuthorizationData();
		if ($this->isHeaderRedirectionSupported()) {
			$data->setRedirectionUrl($this->getRedirectionUrl());
		} else {
			$data->setFormActionUrl($this->getFormActionUrl());
			$data->setHiddenFormFields($this->getHiddenFormFields());
		}
		return $data;
	}

	/**
	 * @return \Customweb_Payment_Authorization_PaymentPage_IAdapter
	 */
	public function getAdapter()
	{
		return parent::getAdapter();
	}
}