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
 *
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Method_DirectDebitsMethod extends Customweb_Realex_Method_AbstractAlternativeMethod {
	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
	
		/* @var $aliasTransaction Customweb_Realex_Authorization_Transaction */
		/* @var $failedTransaction Customweb_Realex_Authorization_Transaction */

		$elements = array();
		
		$elements[] = Customweb_Form_ElementFactory::getAccountOwnerNameElement("Holder", $orderContext->getBillingFirstName() . " " . $orderContext->getBillingLastName());
		$elements[] = Customweb_Form_ElementFactory::getAccountNumberElement("Iban");
		$elements[] = $this->getBicField();
		
		return $elements;
	}
	
	
	private function getBicField(){
		$control = new Customweb_Form_Control_TextInput("Bic");
		$control->addValidator(new Customweb_Form_Validator_NotEmpty($control, Customweb_I18n_Translation::__("You have to enter the BIC.")));
		$control->setAutocomplete(false);
		$element = new Customweb_Form_Element(
				Customweb_I18n_Translation::__('BIC'),
				$control,
				Customweb_I18n_Translation::__('Please enter the BIC number.')
		);

		return $element;
	}
	
	public function getPaymentMethodHashParam($transaction){
		return "elv";
	}
	
	public function getPaymentMethodDetailsElement($transaction){
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		$element = "<paymentmethod>" . $this->getPaymentMethodHashParam($transaction) . "</paymentmethod>" .
			"<paymentmethoddetails>" . 		
				"<Account>" .
					"<Holder>" . $transaction->getAccountHolder() . "</Holder>" .
					"<Iban>" . $transaction->getIban() . "</Iban>" .
					"<Bic>" . $transaction->getBic() . "</Bic>" .
					"<Country>" . $transaction->getTransactionContext()->getOrderContext()->getBillingCountryIsoCode() . "</Country>" .
				"</Account>" .
				"<Descriptor>customwebtest</Descriptor>" .
			"</paymentmethoddetails>";
		
		//reset these the iban and bic value -> data privacy 
		$transaction->setIban("");
		$transaction->setBic("");
		
		return $element;
	}
	
	public function setTransactionDataOnUserInput($parameters, $transaction) {
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		if (!isset($parameters['Holder'])) {
			throw new Exception(Customweb_I18n_Translation::__("No account holder set."));
		}
	
		if (!isset($parameters['Iban'])) {
			throw new Exception(Customweb_I18n_Translation::__("No IBAN set."));
		}
		
		if (!isset($parameters['Bic'])) {
			throw new Exception(Customweb_I18n_Translation::__("No IBAN set."));
		}
			
		$transaction->setAccountHolder($parameters['Holder']);
		$transaction->setIban($parameters['Iban']);
		$transaction->setBic($parameters['Bic']);
		
		if(is_null($parameters)){
			return array();
		}
		
		return $parameters;
	}
}