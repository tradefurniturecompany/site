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
final class Customweb_Realex_Method_PaypalMethod extends Customweb_Realex_Method_AbstractAlternativeMethod {
	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
		/* @var $aliasTransaction Customweb_Realex_Authorization_Transaction */
		/* @var $failedTransaction Customweb_Realex_Authorization_Transaction */

		$elements = array();
		
		return $elements;
	}
	
	public function getPaymentMethodHashParam($transaction){
		return "paypal";
	}
	
	public function getPaymentMethodDetailsElement($transaction){
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		$payerId = $transaction->getPayerId();
		if(isset($payerId)){
			$element = "<pasref>" . $transaction->getPaypalPasRef() . "</pasref>" .
					"<paymentmethod>" . $this->getPaymentMethodHashParam($transaction) . "</paymentmethod>" .
					"<paymentmethoddetails>" .
						"<Token>" . $transaction->getToken() . "</Token>" .
						"<PayerID>" . $transaction->getPayerId() . "</PayerID>" .
					"</paymentmethoddetails>";
		}else{
			$orderContext = $transaction->getTransactionContext()->getOrderContext();
			
			$billinState = $orderContext->getBillingState();
			$billinStateXml = "";
			if(isset($billinState)){
				$billinStateXml = "<StateOrProvince>" . $orderContext->getBillingState() ."</StateOrProvince>";
			}
			
			$parameterArray = $transaction->getAuthorizationParameters();
			$parameterArray['cw_transaction_id'] = $transaction->getExternalTransactionId();
			
			$returnUrl = $this->container->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "paypal", $parameterArray);
			$returnUrl = str_replace("&", "&amp;", $returnUrl);
			
			$element = "<paymentmethod>" . $this->getPaymentMethodHashParam($transaction) . "</paymentmethod>" .
					"<paymentmethoddetails>" .
						"<ReturnURL>". $returnUrl ."</ReturnURL>" .
						"<CancelURL>". $returnUrl ."</CancelURL>" .
						"<NoShipping>1</NoShipping>" .
					"</paymentmethoddetails>";
		}
		
		return $element;
	}
	
	public function setTransactionDataOnUserInput($parameters, $transaction) {
		
		if(is_null($parameters)){
			return array();
		}
		return $parameters;
	}
}