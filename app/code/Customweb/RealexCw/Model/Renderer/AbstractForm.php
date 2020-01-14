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

class AbstractForm extends \Customweb_Form_Renderer
{
	/**
	 * @var string
	 */
	protected $formName;

	public function __construct()
	{
		$this->setElementLabelCssClass('label');
	}

	public function renderForm(\Customweb_IForm $form)
	{
		$this->formName = $form->getMachineName();

		$output = '<form class="' . $this->getFormCssClass() . '" action="' . $form->getTargetUrl() . '" method="' . $form->getRequestMethod() . '"
				target="' . $form->getTargetWindow() . '" id="' . $form->getId() . '" name="' . $form->getMachineName() . '">';

		$output .= $this->renderElementGroups($form->getElementGroups());

		$token = $this->getFormToken($form);
		if ($token !== null) {
			$output .= '<input type="hidden" name="' . self::FORM_TOKEN_FIELD_NAME . '" value="' . $token . '" />';
		}

		$output .= $this->renderButtons($form->getButtons(), $this->formName);
		$output .= '</form>';

		if ($this->isAddJs()) {
			$output .= '<script type="text/javascript">' . "\n";
			$output .= $this->renderElementsJavaScript($form->getElements(), $this->formName);
			$output .= "\n</script>";
		}
		return $output;

		//return parent::renderForm($form);
	}

	/**
	 * @param string $referenceTo
	 * @param string $label
	 * @param string $class
	 * @return string
	 */
	protected function renderLabel($referenceTo, $label, $class)
	{
		$for = '';
		if (!empty($referenceTo)) {
			$for = ' for="' . $referenceTo . '" ';
		}
		return '<label class="' . $class . '" ' . $for . '><span>' . $label . '</span></label>';
	}

	/**
	 * @param \Customweb_Form_IElement $element
	 * @return string
	 */
	protected function renderRequiredTag(\Customweb_Form_IElement $element)
	{
		return '';
	}

	public function renderElementsJavaScript(array $elements, $jsFunctionPostfix = '')
	{
		$js = '';
		foreach ($elements as $element) {
			$js .= $element->getJavaScript() . "\n";
		}
		$js .= "\n";
		$js .= $this->renderValidatorCallbacks($elements, $jsFunctionPostfix);
		$js .= $this->registerOnSubmitEvent();
		return $js;
	}

	protected function renderRegisterCallback($jsFunctionPostfix){
		$postfix = $jsFunctionPostfix;
		if($this->getNamespacePrefix() !== null){
			$postfix = $this->getNamespacePrefix().$postfix;
		}

		$registerJs = "require(['jquery', 'domReady', 'Customweb_RealexCw/js/form'], function($, domReady, Form){\n";
		$registerJs .= "	domReady(function(){\n";
		$registerJs .= "		Form.Validation.register('{$this->formName}', '{$postfix}'";
		$registerJs .= "	);\n";
		$registerJs .= "})});\n";
		return $registerJs;
	}

	protected function renderValidatorCallbacks(array $elements, $jsFunctionPostfix)
	{
		$validationJs = $this->renderRegisterCallback($jsFunctionPostfix);
		$validationJs .= parent::renderValidatorCallbacks($elements, $jsFunctionPostfix);
		return $validationJs;
	}

	protected function registerOnSubmitEvent()
	{
		$eventJs = "require(['jquery', 'domReady', 'Customweb_RealexCw/js/form', 'mage/template'], function($, domReady, Form, mageTemplate){\n";
		$eventJs .= "	domReady(function(){\n";
		$eventJs .= "		Form.fieldErrorTmpl = mageTemplate('<div for=\"<%- id %>\" generated=\"true\" class=\"mage-error\" id=\"<%- id %>-error\"><%- message %></div>');\n";
		$eventJs .= "		$('form[name=\"{$this->formName}\"]').on('submit', function(event){\n";
		$eventJs .= "			event.stopPropagation();\n";
		$eventJs .= "			Form.validate('{$this->formName}');";

		$eventJs .= "			return false;\n";
		$eventJs .= "		});\n";
		$eventJs .= "	});\n";
		$eventJs .= "});\n";
		return $eventJs;
	}
}