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

class ControlCssClassResolver extends \Customweb\RealexCw\Model\Renderer\ControlCssClassResolver
{
	public function resolveClass(\Customweb_Form_Control_IControl $control, \Customweb_Form_IElement $element)
	{
		$classes = parent::resolveClass($control, $element);
		if ($control instanceof \Customweb_Form_Control_TextInput
			|| $control instanceof \Customweb_Form_Control_Password
			|| $control instanceof \Customweb_Form_Control_Textarea) {
			$classes .= ' admin__control-text';
		} elseif ($control instanceof \Customweb_Form_Control_Select) {
			$classes .= ' admin__control-select';
		} elseif ($control instanceof \Customweb_Form_Control_MultiSelect) {
			$classes .= ' admin__control-multiselect';
		} elseif ($control instanceof \Customweb_Form_Control_SingleCheckbox
				|| $control instanceof \Customweb_Form_Control_MultiCheckbox) {
			$classes .= ' admin__control-checkbox';
		} elseif ($control instanceof \Customweb_Form_Control_Radio) {
			$classes .= ' admin__control-radio';
		}
		return $classes;
	}
}