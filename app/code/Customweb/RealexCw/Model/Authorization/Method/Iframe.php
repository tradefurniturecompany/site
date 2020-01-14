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

class Iframe extends AbstractMethod
{
	protected function getAdapterInterfaceName()
	{
		return 'Customweb_Payment_Authorization_Iframe_IAdapter';
	}

	/**
	 * @return string
	 */
	public function getFormActionUrl()
	{
		return $this->getContext()->getTransaction()->getStore()->getUrl('realexcw/checkout/iframe', ['_secure' => true]);
	}

	/**
	 * @return array
	 */
	public function getHiddenFormFields()
	{
		return $this->getContext()->getParameters();
	}

	/**
	 * @return string
	 */
	public function getIframeUrl()
	{
		$result = $this->getAdapter()->getIframeUrl($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return (string) $result;
	}

	/**
	 * @return int
	 */
	public function getIframeHeight()
	{
		$result = $this->getAdapter()->getIframeHeight($this->getContext()->getTransaction()->getTransactionObject(), $this->getContext()->getParameters());
		$this->getContext()->getTransaction()->save();
		return $result;
	}

	/**
	 * @return \Customweb_Payment_Authorization_Iframe_IAdapter
	 */
	public function getAdapter()
	{
		return parent::getAdapter();
	}
}