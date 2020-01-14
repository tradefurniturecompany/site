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
 */


/**
 * This validator is only for {@link Customweb_Form_Control_Radio}.
 * It checks if at least on option is selected.
 * 
 * @author Thomas Hunziker
 *
 */
class Customweb_Form_Validator_RadioValidator implements Customweb_Form_Validator_IValidator {
	
	private $control;
	private $errorMessage;
	
	/**
	 * 
	 * @param Customweb_Form_Control_Radio $control
	 * @param string $errorMessage The error message presented to the user, in case the input is not valid
	 */
	public function __construct(Customweb_Form_Control_Radio $control, $errorMessage) {
		$this->control = $control;
		$this->errorMessage = $errorMessage;
	}
	
	/**
	 * The control to which this validator is assigned to.
	 * 
	 * @return Customweb_Form_Control_Radio
	 */
	public function getControl() {
		return $this->control;
	}
	
	/**
	 * This method sets the control on which the validation is executed.
	 * 
	 * @param Customweb_Form_Control_Radio $control
	 * @return Customweb_Form_Validator_Abstract
	 */
	public function setControl(Customweb_Form_Control_Radio $control) {
		$this->control = $control;
		return $this;
	}
	
	/**
	 * The error message presented to the user in case the user input
	 * is not valid.
	 * 
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	
	/**
	 * Sets the error message.
	 * 
	 * @param string $errorMessage Error message
	 * @return Customweb_Form_Validator_Abstract
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Customweb_Form_Validator_IValidator::getCallbackJs()
	 */
	public function getCallbackJs() {
		$js = 'function (resultCallback, element) {
			var selected = false;
			';
		$i = 0;
		foreach($this->getControl()->getOptions() as $key => $name){
			$js .= 'var tmpE_'.$i.' = document.getElementById("'.$this->getControl()->getControlId().'-'.$key.'");
				if(tmpE_'.$i.'.checked){
					selected = true;
				}
			';
			$i++;		}
		
		$js .= 'if(selected) {
					resultCallback(true)	
				}
				else{
					resultCallback(false, "' . str_replace('"', '\"', $this->getErrorMessage()).'");	
				}

			};';
		return $js;
	}

}