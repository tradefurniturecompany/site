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

namespace Customweb\RealexCw\Model\Config;

class Structure
{
	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	private $container;

	/**
	 * URL builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	private $_urlBuilder;

	public function __construct(
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Magento\Framework\UrlInterface $urlBuilder
	) {
		$this->container = $container;
		$this->_urlBuilder = $urlBuilder;
	}

	/**
	 * Get all forms
	 *
	 * @return array
	 */
	public function getForms()
	{
		return $this->getFormAdapter()->getForms();
	}

	/**
	 * @param string $machineName
	 * @return \Customweb_Payment_BackendOperation_IForm
	 * @throws \Exception
	 */
	public function getForm($machineName)
	{
		foreach ($this->getForms() as $form) {
			if ($form->getMachineName() == $machineName) {
				return $form;
			}
		}
		throw new \Exception(\Customweb_Core_String::_("Could not find form with form name '@name'.")->format(['@name' => $machineName]));
	}

	/**
     * Retrieve first available form
     *
     * @return \Customweb_Payment_BackendOperation_IForm
     */
	public function getFirstForm() {
		foreach ($this->getForms() as $form) {
			return $form;
		}
	}

	/**
	 * @return \Customweb_Payment_BackendOperation_Form_IAdapter
	 */
	public function getFormAdapter()
	{
		return $this->container->getBean('Customweb_Payment_BackendOperation_Form_IAdapter');
	}
}