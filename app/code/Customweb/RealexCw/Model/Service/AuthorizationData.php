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

namespace Customweb\RealexCw\Model\Service;

class AuthorizationData implements \Customweb\RealexCw\Api\Data\AuthorizationDataInterface {

	private $formActionUrl;

	private $hiddenFormFields = [];

	private $ajaxFileUrl;

	private $javaScriptCallbackFunction;

	private $redirectionUrl;

	public function getFormActionUrl()
	{
		return $this->formActionUrl;
	}

	public function getHiddenFormFields()
	{
		return $this->hiddenFormFields;
	}

	public function getAjaxFileUrl()
	{
		return $this->ajaxFileUrl;
	}

	public function getJavaScriptCallbackFunction()
	{
		return $this->javaScriptCallbackFunction;
	}

	public function getRedirectionUrl()
	{
		return $this->redirectionUrl;
	}

	public function setFormActionUrl($formActionUrl)
	{
		$this->formActionUrl = $formActionUrl;
		return $this;
	}

	public function setHiddenFormFields(array $hiddenFormFields)
	{
		$fields = [];
		foreach ($hiddenFormFields as $key => $value) {
			$field = new \Customweb\RealexCw\Model\Service\AuthorizationFormField();
			$field->setKey((string) $key);
			if (is_array($value)) {
				$field->setValue(array_map(function($e){
					return (string) $e;
				}, $value));
			} else {
				$field->setValue((string) $value);
			}
			$fields[] = $field;
		}
		$this->hiddenFormFields = $fields;
		return $this;
	}

	public function setAjaxFileUrl($ajaxFileUrl)
	{
		$this->ajaxFileUrl = $ajaxFileUrl;
		return $this;
	}

	public function setJavaScriptCallbackFunction($javaScriptCallbackFunction)
	{
		$this->javaScriptCallbackFunction = $javaScriptCallbackFunction;
		return $this;
	}

	public function setRedirectionUrl($redirectionUrl)
	{
		$this->redirectionUrl = $redirectionUrl;
		return $this;
	}

}