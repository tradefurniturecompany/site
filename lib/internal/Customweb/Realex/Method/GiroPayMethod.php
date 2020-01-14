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
final class Customweb_Realex_Method_GiroPayMethod extends Customweb_Realex_Method_AbstractAlternativeMethod {
	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
		/* @var $aliasTransaction Customweb_Realex_Authorization_Transaction */
		/* @var $failedTransaction Customweb_Realex_Authorization_Transaction */

		$elements = array();
		
		$elements[] = Customweb_Form_ElementFactory::getAccountNumberElement("bank_account");
		
		return $elements;
	}
	
	public function getPaymentMethodHashParam($transaction){
		return "giropay";
	}
	
	public function getPaymentMethodDetailsElement($transaction){
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		$returnUrl = $this->container->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "common", array());
		$returnUrl = str_replace("&", "&amp;", $returnUrl);
		
		$element = "<paymentmethod>" . $this->getPaymentMethodHashParam($transaction) . "</paymentmethod>" .
			"<paymentmethoddetails>" .
				"<ReturnURL>" . $returnUrl . "</ReturnURL>" .
				"<BankAccount>" .
					"<Bank>" . $transaction->getGiroBankNumber() . "</Bank>" .
					"<Country>" . $transaction->getTransactionContext()->getOrderContext()->getBillingCountryIsoCode() . "</Country>" . 
				"</BankAccount>" .
				"<Descriptor>customwebtest</Descriptor>" .
			"</paymentmethoddetails>";
		
		//reset the bank number -> data privacy 
		$transaction->setGiroBankNumber("");
		
		return $element;
	}
	
	public function setTransactionDataOnUserInput($parameters, $transaction) {
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		if (!isset($parameters['bank_account'])) {
			throw new Exception(Customweb_I18n_Translation::__("No account holder set."));
		}
			
		$transaction->setGiroBankNumber($parameters['bank_account']);
		
		if(is_null($parameters)){
			return array();
		}
		
		return $parameters;
	}
}