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

namespace Customweb\RealexCw\Model\Renderer;

class CheckoutForm extends FrontendForm
{
	/**
	 * @param string $paymentMethodCode
	 */
	public function __construct($paymentMethodCode)
	{
		parent::__construct();
		$this->formName = $paymentMethodCode;
	}

	public function renderControlPrefix(\Customweb_Form_Control_IControl $control, $controlTypeClass)
	{
		$classes = [$controlTypeClass];
		if ($control instanceof \Customweb_Form_Control_MultiControl) {
			$this->inMultiControl = true;
		}
		return '<div class="control ' . implode(' ', $classes) . '" id="' . $control->getControlId() . '-wrapper">';
	}

	public function renderElementsJavaScript(array $elements, $jsFunctionPostfix = '')
	{
		$js = '';
		foreach ($elements as $element) {
			$js .= $element->getJavaScript() . "\n";
		}
		$js .= "\n";
		$js .= $this->renderValidatorCallbacks($elements, $jsFunctionPostfix);
		return $js;
	}

	protected function renderRegisterCallback($jsFunctionPostfix){
		$postfix = $jsFunctionPostfix;
		if($this->getNamespacePrefix() !== null){
			$postfix = $this->getNamespacePrefix().$postfix;
		}

		$registerJs = "require(['jquery', 'domReady', 'Customweb_RealexCw/js/checkout', 'mage/template'], function($, domReady, Form, mageTemplate){\n";
		$registerJs .= "	Form.fieldErrorTmpl = mageTemplate('<div for=\"<%- id %>\" generated=\"true\" class=\"mage-error\" id=\"<%- id %>-error\"><%- message %></div>');\n";
		$registerJs .= "	domReady(function(){\n";
		$registerJs .= "		Form.Validation.register('{$this->formName}', '{$postfix}'";
		$registerJs .= "	);\n";
		$registerJs .= "})});\n";
		return $registerJs;
	}
}