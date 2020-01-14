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
final class Customweb_Realex_Method_IdealMethod extends Customweb_Realex_Method_AbstractAlternativeMethod {
	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
		/* @var $aliasTransaction Customweb_Realex_Authorization_Transaction */
		/* @var $failedTransaction Customweb_Realex_Authorization_Transaction */

		$elements = array();
		
		$elements[] = Customweb_Form_ElementFactory::getBankNameElement("bank_name");
		
		return $elements;
	}
	
	public function getPaymentMethodHashParam($transaction){
		return "giropay";
	}
	
	public function getPaymentMethodDetailsElement($transaction){
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		$element = "<paymentmethod>" . $this->getPaymentMethodHashParam($transaction) . "</paymentmethod>" .
			"<paymentmethoddetails>" . 		
				"<BankAccount>" .
					"<Bank>" . $transaction->getBankName() . "</Bank>" .
					"<Country>" . $transaction->getTransactionContext()->getOrderContext()->getBillingCountryIsoCode() . "</Country>" .
				"</BankAccount>" .
			"</paymentmethoddetails>";
		
		//reset these the iban and bic value -> data privacy 
		$transaction->setIdealBankName("");
		
		return $element;
	}
	
	public function setTransactionDataOnUserInput($parameters, $transaction) {
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		if (!isset($parameters['bank_name'])) {
			throw new Exception(Customweb_I18n_Translation::__("No account holder set."));
		}
			
		
		$transaction->setIdealBankName($parameters['bank_name']);
		
		if(is_null($parameters)){
			return array();
		}
		
		return $parameters;
	}
}