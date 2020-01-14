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

namespace Customweb\RealexCw\Model\Renderer\Adminhtml;

class BackendForm extends \Customweb\RealexCw\Model\Renderer\AbstractForm
{
	const STORAGE_CONFIG_SPACE = 'backend_form';
	const STORAGE_CONFIG_STATE_KEY = 'config_state';

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	private $_container;

	/**
	 * @var \Magento\Framework\Data\Form\FormKey
	 */
	protected $_formKey;

	/**
	 * @var string
	 */
	private $formId = null;

	/**
	 * @var boolean
	 */
	private $showScope = true;

	/**
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Magento\Framework\Data\Form\FormKey $formKey
	 */
	public function __construct(
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Magento\Framework\Data\Form\FormKey $formKey
	) {
		parent::__construct();

		$this->_container = $container;
		$this->_formKey = $formKey;

		$this->setFormCssClass('realexcw_form');
		$this->setElementCssClass('');
		$this->setElementLabelCssClass('');
		$this->setDescriptionCssClass('');
		$this->setElementErrorCssClass('');
		$this->setElementScopeCssClass('');
		$this->setControlCssClass('');
		$this->setOptionCssClass('');
		$this->setErrorMessageCssClass('');
	}

	public function setShowScope($flag) {
		$this->showScope = (boolean) $flag;
		return $this;
	}

	public function renderElementPrefix(\Customweb_Form_IElement $element)
	{
		if ($element instanceof \Customweb_Form_HiddenElement) {
			return '';
		}

		$classes = $this->getElementCssClass();
		$classes .= ' ' . $element->getElementIntention()->getCssClass();

		$errorMessage = $element->getErrorMessage();
		if (!empty($errorMessage)) {
			$classes .= ' ' . $this->getElementErrorCssClass();
		}

		$output = '<tr class="' . $classes . '" id="' . $element->getElementId() . '" ' . (!$this->isElementVisible($element) ? 'style="display: none;"' : '') . '>';
		if ($element instanceof \Customweb_Form_WideElement) {
			$output .= '<td class="value" colspan="2">';
		}
		return $output;
	}

	public function renderElementPostfix(\Customweb_Form_IElement $element)
	{
		if ($element instanceof \Customweb_Form_HiddenElement) {
			return '';
		}
		$output = '';
		if ($this->showScope) {
			$output .= '</td><td class="scope-label">';
			if ($element->getControl() instanceof \Customweb_Form_Control_IEditableControl) {
				$output .= '[' . ($element->isGlobalScope() ? 'GLOBAL' : 'STORE VIEW') . ']';
			}
		}
		$output .= '</td><td></td></tr>';
		return $output;
	}

	public function renderElementLabel(\Customweb_Form_IElement $element) {
		return parent::renderElementLabel($element) . '<td class="value field">';
	}

	protected function renderLabel($referenceTo, $label, $class)
	{
		return '<td class="label">' . parent::renderLabel($referenceTo, $label, $class) . '</td>';
	}

	public function renderElementAdditional(\Customweb_Form_IElement $element)
	{
		$output = '';

		$errorMessage = $element->getErrorMessage();
		if (!empty($errorMessage)) {
			$output .= $this->renderElementErrorMessage($element);
		}

		$description = $element->getDescription();
		if (!empty($description)) {
			$output .= $this->renderElementDescription($element);
		}

		if (!$element->isGlobalScope()) {
			$output .= $this->renderElementScope($element);
		}

		return $output;
	}

	protected function renderElementDescription(\Customweb_Form_IElement $element)
	{
		return '<p class="note ' . $this->getDescriptionCssClass() . '"><span>' . $element->getDescription() . '</span></p>';
	}

	protected function renderElementScope(\Customweb_Form_IElement $element)
	{
		$storeHierarchy = $this->_container->getBean('Customweb_Payment_IConfigurationAdapter')->getStoreHierarchy();
		if ($storeHierarchy == null) {
			return '';
		}
		$output = '</td><td class="use-default">';
		if ($element->getControl() instanceof \Customweb_Form_Control_IEditableControl) {
			$output .= $this->renderElementScopeControl($element);
		}
		return $output;
	}

	/**
	 * @param Customweb_Form_IElement $element
	 * @return string
	 */
	protected function renderElementScopeControl(\Customweb_Form_IElement $element)
	{
		$scopeControlId = $element->getControl()->getControlId() . '-scope';
		$scopeControlName = implode('_', $element->getControl()->getControlNameAsArray());
		$output = '';
		$output .= '<input class="use-default-checkbox"
			type="checkbox" ' . ($element->isInherited() ? 'checked="checked"' : '') . '
			name="default[' . $scopeControlName . ']"
			id="' . $scopeControlId . '"
			value="default"
			' . ($this->isAddJs() ? 'onclick="toggleValueElements(this, Element.previous(this.parentNode))"' : '') . ' />';
		$output .= '<label for="' . $scopeControlId . '">' . __('Use Default') . '</label>';
		return $output;
	}

	public function renderRawElements(array $elements)
	{
		$result = '';
		foreach($elements as $element) {
			if ($this->isElementVisible($element)) {
				if ($this->getNamespacePrefix() !== NULL) {
					$element->applyNamespacePrefix($this->getNamespacePrefix());
				}

				if ($this->getControlCssClassResolver() !== NULL) {
					$element->applyControlCssResolver($this->getControlCssClassResolver());
				}
				$result .= $element->render($this);
			}
		}
		return $result;
	}

	public function renderElementGroupPrefix(\Customweb_Form_IElementGroup $elementGroup)
	{
		return '<div class="section-config">';
	}

	public function renderElementGroupPostfix(\Customweb_Form_IElementGroup $elementGroup)
	{
		$machineName = $elementGroup->getMachineName();
		if (empty($machineName)) {
			$machineName = $elementGroup->getId();
		}

		$output = '';
		$output .= '</tbody>';
		$output .= '</table>';
		$output .= '</fieldset>';

		if ($this->isAddJs()) {
			$output .= '<script type="text/javascript">' . "\n";
			$output .= 'require(["mage/mage", "prototype", "domReady!"], function(){';
			$output .= 'Fieldset.applyCollapse(\'' . $machineName . '\')' . "\n";
			$output .= '});';
			$output .= "\n</script>";
		}
		$output .= '</div>';

		return $output;
	}

	public function renderElementGroupTitle(\Customweb_Form_IElementGroup $elementGroup)
	{
		$machineName = $elementGroup->getMachineName();
		if (empty($machineName)) {
			$machineName = $elementGroup->getId();
		}

		$output = '';
		$title = $elementGroup->getTitle();
		if (! empty($title)) {
			$cssClass = $this->getElementGroupTitleCssClass();
			$output .= '<div class="entry-edit-head admin__collapsible-block ' . $cssClass . '">';
			$output .= '<a id="' . $machineName . '-head" onclick="Fieldset.toggleCollapse(\'' . $machineName . '\'); return false;">' . $title . '</a>';
			$output .= '</div>';
		} else {
			$output .= '<span class="admin__collapsible-block" id="' . $machineName . '-head"></span>';
		}
		$output .= '<input id="' . $machineName . '-state" name="config_state[' . $this->formName . '][' . $machineName . ']" type="hidden" value="' . $this->getConfigState($elementGroup) . '" />';
		$output .= '<fieldset class="config admin__collapsible-block" id="' . $machineName . '">';
		$output .= '<table cellspacing="0" class="form-list">';
		$output .= '<colgroup class="label"></colgroup>';
		$output .= '<colgroup class="value"></colgroup>';
		$output .= '<colgroup class="use-default"></colgroup>';
		if ($this->showScope) {
			$output .= '<colgroup class="scope-label"></colgroup>';
		}
		$output .= '<colgroup></colgroup>';
		$output .= '<tbody>';
		return $output;
	}

	public function renderForm(\Customweb_IForm $form)
	{
		$this->formName = $form->getMachineName();
		$this->formId = $form->getId();

		$output = '<form class="active ' . $this->getFormCssClass() . '" action="' . $form->getTargetUrl() . '" method="' . $form->getRequestMethod() . '"
				target="' . $form->getTargetWindow() . '" id="' . $form->getId() . '" name="' . $form->getMachineName() . '">';

		$output .= '<div><input type="hidden" name="form_key" value="'. $this->_formKey->getFormKey() . '" /></div>';
		$output .= '<div class="accordion">';
		$output .= '<div class="entry-edit form-inline">';

		$output .= $this->renderElementGroups($form->getElementGroups());

		$output .= '</div>';
		$output .= '</div>';
		$output .= '<input type="hidden" name="button" id="' . $form->getId() . '-button" value="" />';

		$output .= '</form>';

		if ($this->isAddJs()) {
			$output .= '<script type="text/javascript">' . "\n";
			$output .= 'require(["prototype", "domReady!"], function(){';
			$output .= '$$(\'.use-default-checkbox\').each(function(element){ toggleValueElements(element, Element.previous(element.parentNode)); });' . "\n";
			$output .= '});';
			$output .= $this->renderElementsJavaScript($this->getVisibleElements($form->getElements()), $this->formName);
			$output .= "\n</script>";
		}
		return $output;
	}

	protected function getVisibleElements(array $elements){
		$visible = array();
		foreach($elements as $element){
			if($this->isElementVisible($element)){
				$visible[] = $element;
			}
		}
		return $visible;
	}
	
	/**
	 * @param \Customweb_Form_IElement $element
	 * @return bool
	 */
	protected function isElementVisible($element)
	{
		$storeHierarchy = $this->_container->getBean('Customweb_Payment_IConfigurationAdapter')->getStoreHierarchy();
		return $storeHierarchy == null || !$element->isGlobalScope();
	}

	/**
	 * @param \Customweb_Form_IElementGroup $elementGroup
	 * @return int
	 */
	protected function getConfigState(\Customweb_Form_IElementGroup $elementGroup)
	{
		/* @var $storage \Customweb_Storage_IBackend */
		$storage = $this->_container->getBean('Customweb_Storage_IBackend');
		if (!($storage instanceof \Customweb_Storage_IBackend)) {
			return 1;
		}

		$configState = $storage->read(self::STORAGE_CONFIG_SPACE, self::STORAGE_CONFIG_STATE_KEY);
		if (is_array($configState) && array_key_exists($this->formName, $configState) && array_key_exists($elementGroup->getMachineName(), $configState[$this->formName])) {
			return $configState[$this->formName][$elementGroup->getMachineName()];
		} else {
			return 1;
		}
	}
}