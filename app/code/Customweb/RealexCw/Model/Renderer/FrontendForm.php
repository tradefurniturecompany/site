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

class FrontendForm extends AbstractForm
{
	/**
	 * @var boolean
	 */
	private $inMultiControl = false;

	public function __construct()
	{
		parent::__construct();
		$this->setControlCssClassResolver(new \Customweb\RealexCw\Model\Renderer\ControlCssClassResolver());
	}

	public function renderElementGroupPrefix(\Customweb_Form_IElementGroup $elementGroup)
	{
		return '<fieldset class="realexcw_fieldset fieldset">';
	}

	public function renderElementGroupTitle(\Customweb_Form_IElementGroup $elementGroup)
	{
		$output = '';
		$title = $elementGroup->getTitle();
		if (! empty($title)) {
			$cssClass = $this->getCssClassPrefix() . $this->getElementGroupTitleCssClass();
			$output .= '<legend class="legend ' . $cssClass . '"><span>' . $title . '</span></legend>';
		}
		return $output;
	}

	public function renderElementPrefix(\Customweb_Form_IElement $element)
	{
		$classes = ['field'];
		$classes[] = $element->getElementIntention()->getCssClass();
		if ($element->isRequired()) {
			$classes[] = 'required';
		}
		return '<div class="' . implode(' ', $classes) . '" id="' . $element->getElementId() . '">';
	}

	public function renderControlPrefix(\Customweb_Form_Control_IControl $control, $controlTypeClass)
	{
		$classes = [$controlTypeClass];
		if ($this->inMultiControl) {
			$classes[] = 'field';
		}
		if ($control instanceof \Customweb_Form_Control_MultiControl) {
			$control->setCssClass('fields group');
			$this->inMultiControl = true;
		}
		return '<div class="control ' . implode(' ', $classes) . '" id="' . $control->getControlId() . '-wrapper">';
	}

	public function renderControlPostfix(\Customweb_Form_Control_IControl $control, $controlTypeClass)
	{
		if ($control instanceof \Customweb_Form_Control_MultiControl) {
			$this->inMultiControl = false;
		}
		return '</div>';
	}

	/**
	 * @param Customweb_Form_IElement $element
	 * @return string
	 */
	protected function renderElementDescription(\Customweb_Form_IElement $element)
	{
		return '<div class="' . $this->getDescriptionCssClass() . '"><small>' . $element->getDescription() . '</small></div>';
	}

	protected function renderButtons(array $buttons, $jsFunctionPostfix = '')
	{
		$output = '<div class="actions-toolbar">';
		foreach ($buttons as $button) {
			$output .= $this->renderButton($button, $jsFunctionPostfix);
		}
		$output .= '</div>';
		return $output;
	}

	public function renderButton(\Customweb_Form_IButton $button, $jsFunctionPostfix = '')
	{
		$postfix = $jsFunctionPostfix;
		if($this->getNamespacePrefix() !== null){
			$postfix = $this->getNamespacePrefix().$postfix;
		}
				
		$output = '<div class="' . $this->getButtonTypeClass($button) . '">';
		$output .= '<button type="submit" name="button[' . $button->getMachineName() . ']" title="' . $button->getTitle() . '" class="action ' . $this->getButtonTypeClass($button) . '" id="' . $button->getId() . '" ';
		if(!$button->isJSValidationExecuted()){
			$output .= 'onclick="cwValidationRequired'.$postfix.' = false; return true;"';
		}
		else {
			$output .='onclick="cwValidationRequired'.$postfix.' = true; return true;"';
		}				
				
				
		$output	.=	'>';
		$output .= '<span>' . $button->getTitle() . '</span>';
		$output .= '</button>';
		$output .= '</div>';
		return $output;
	}

	/**
	 * @param Customweb_Form_IButton $button
	 * @return string
	 */
	protected function getButtonTypeClass(\Customweb_Form_IButton $button)
	{
		switch ($button->getType()) {
			case \Customweb_Form_IButton::TYPE_CANCEL:
			case \Customweb_Form_IButton::TYPE_DEFAULT:
			case \Customweb_Form_IButton::TYPE_INFO:
				return 'secondary';
			case \Customweb_Form_IButton::TYPE_SUCCESS:
				return 'primary';
		}
	}
}