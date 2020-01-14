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

class CheckoutForm extends \Customweb\RealexCw\Model\Renderer\AbstractForm
{
	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	private $_container;

	/**
	 * @var \Magento\Framework\Data\Form\FormKey
	 */
	protected $_formKey;

	/**
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @param \Magento\Framework\Data\Form\FormKey $formKey
	 */
	public function __construct(
			\Customweb\RealexCw\Model\DependencyContainer $container,
			\Magento\Framework\Data\Form\FormKey $formKey
	) {
		$this->_container = $container;
		$this->_formKey = $formKey;

		$this->setControlCssClassResolver(new \Customweb\RealexCw\Model\Renderer\Adminhtml\ControlCssClassResolver());

		$this->setFormCssClass('realexcw_form');
		$this->setElementCssClass('admin__field field');
		$this->setElementLabelCssClass('label admin__field-label');
		$this->setDescriptionCssClass('note');
		$this->setControlCssClass('admin__field-control control');
	}

	public function renderElementGroupPrefix(\Customweb_Form_IElementGroup $elementGroup)
	{
		return '<div class="admin__fieldset">';
	}

	public function renderElementGroupPostfix(\Customweb_Form_IElementGroup $elementGroup)
	{
		return '</div>';
	}

	public function renderElementPrefix(\Customweb_Form_IElement $element)
	{
		$classes = $this->getCssClassPrefix() . $this->getElementCssClass();
		$classes .= ' ' . $this->getCssClassPrefix() . $element->getElementIntention()->getCssClass();

		$errorMessage = $element->getErrorMessage();
		if (! empty($errorMessage)) {
			$classes .= ' ' . $this->getCssClassPrefix() . $this->getElementErrorCssClass();
		}

		if ($element->isRequired()) {
			$classes .= ' required _required';
		}

		return '<div class="' . $classes . '" id="' . $element->getElementId() . '">';
	}
}