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
final class Customweb_Realex_Util {
	
	private function __construct() {
		// prevent any instantiation of this class
	}
	
	/**
	 * Converts an amount in a certain currency into the lowest denomination.
	 * For example:
	 * In:	14.15 EUR
	 * Out:	1415
	 * 
	 * @param float $amount
	 * @param string $currency
	 * @return int amount
	 */
	public static function formatAmount($amount, $currency) {
		return (int) Customweb_Util_Currency::formatAmount($amount, $currency, '', '');
	}
	
	public static function getAliasString($maskedCardNumber, $cardtype){
		
		$aliasForDisplay = '';
		$len = strlen($maskedCardNumber);
		for ($i = 0; $i < $len; $i = $i + 4) {
			$aliasForDisplay .= substr($maskedCardNumber, $i, 4) . ' ';
		}
		$aliasForDisplay .= ' (' . $cardtype . ')';
		
		return $aliasForDisplay;
	}
	
	public static function maskCardNumber($cardNumber) {
		$len = strlen($cardNumber);
		
		$maskedNumbers = $len - 8;
		if ($maskedNumbers < 0) {
			$maskedNumbers = 0;
		}
		
		return substr($cardNumber, 0, 4) .  str_repeat('x', $maskedNumbers) . substr($cardNumber, $len - 4, 4);
	}
	
	
	public static function sendXML($xml, $endpoint){
		$url = Customweb_Realex_IConstant::BASE_URL . $endpoint;
		$request = new Customweb_Http_Request($url);
		$handler = $request->setMethod('POST')->setBody($xml)->send();
		return $handler;
	}
	
	public static function generateStringToHash(array $elements){
		return implode('.', $elements);
	}
	
	/**
	 * @return string timestamp.Merchant_id.orderid.result.message.pasref.authcode
	 * 				  timestamp.merchantid.orderid.resultcode.resultmessage.pasref.paymentmethod
	 $xml->paymentmethod
	 */
	public static function getResponseXMLStringToHash(SimpleXMLElement $xml, Customweb_Payment_Authorization_AbstractPaymentMethodWrapper $paymentMethod){
		$lastHashElement = $paymentMethod->getLastHashElementName();
		
		$hashElements = array($xml->attributes() ,
				$xml->merchantid,
				$xml->orderid,
				$xml->result,
				$xml->message,
				$xml->pasref,
				$xml->$lastHashElement);
		
		return Customweb_Realex_Util::generateStringToHash($hashElements);
	}
	
	public static function getLowerCaseEncriptionXMLKey($configuration){
		return strtolower($configuration->getEncriptionAlgorithm()) . "hash";
	}
	
	
	/**
	 * Removes all non integer digits and returns the concatenated string like zip|address
	 *
	 * @param string $zip billing zip code
	 * @param string $addressLine first line of billing address
	 * @param string $country The applicable country (supports GB, CA, and US, returns empty otherwise)
	 * @return string
	 */
	public static function getAVSParameterValue($zip, $addressLine, $country){
		switch($country) {
			case 'GB':
				$zip = filter_var($zip, FILTER_SANITIZE_NUMBER_INT);
				$addressLine = filter_var($address, FILTER_SANITIZE_NUMBER_INT);
				break;
			case 'US':
			case 'CA':
				break;
			default:
				return '';
		}
	
		return  $zip . '|' . $addressLine;
	}
	
	
	/**
	 * This method returns a more detailed error message depending on the given
	 * return code and message. The error is translated in cases the customer
	 * sees the message.
	 *
	 * @param string $returnCode The code returned by the remote interface.
	 * @param string $message  The message returned by the remote interface.
	 * @return Customweb_Payment_Authorization_IErrorMessage Error Message
	 */
	public static final function getErrorMessage($returnCode, $message) {
		if ($returnCode == Customweb_Realex_IConstant::STATUS_SUCCESSFUL) {
			return null;
		}
	
		$contactMerchant = ' ' . Customweb_I18n_Translation::__("Please contact the merchant.");
		$generalUserMessage = Customweb_I18n_Translation::__("There occours an error with the payment system.");
		$contactRealex = Customweb_I18n_Translation::__("You may need to contact Realex for further assistance.");
		$outageMessage = Customweb_I18n_Translation::__("The paymnet system seems currently to be offline. Please try it later again.");
		$outagePersists = Customweb_I18n_Translation::__("If this error persists please contact Realex.");
	
		// Some of the messages can not only be identified by the return code, hence we need to filter them
		// by the message it self.
		switch(strtoupper($message)) {
			case 'CANCELLED CARD':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Your card has been cancelled."),
					Customweb_I18n_Translation::__("The customer's card has been cancelled.")
				);
			case 'CARD EXPIRED':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Your card is expired."),
					Customweb_I18n_Translation::__("The customer's card is expired.")
				);
			case 'DECLINED':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Your card has been declined by a unkown reason."),
					Customweb_I18n_Translation::__("The customer's has been declined by a unkown reason.")
				);
			case 'INVALID AMOUNT':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("The amount cannot be charged on your card."),
					Customweb_I18n_Translation::__("The amount cannot be charged on the customer's card.")
				);
			case 'INVALID CARD NO.':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Your card number seems to be invalid."),
					Customweb_I18n_Translation::__("The customer's card number seems to be invalid.")
				);
			case 'INVALID CURRENCY':
				return new Customweb_Payment_Authorization_ErrorMessage(
				Customweb_I18n_Translation::__("The amount in the choosen currency could not be charged."),
				Customweb_I18n_Translation::__("The amount in the given currency could not be charged.")
				);
			case 'INVALID EXP DATE':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("The expiry date is in a invalid format.") . $contactMerchant,
					Customweb_I18n_Translation::__("The expiry date is in a invalid format.")
				);
			case 'INVALID MERCHANT':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The configured merchant was not found.") . $contactRealex
				);
			case 'INVALID TRANS':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The transaction was not valid. The bank refuse it.") . $contactRealex
				);
			case 'NOT AUTHORISED':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The merchant seems not be authorized to execute the transaction.") . $contactRealex
				);
			case 'RETAILER UNKNOWN':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The retailer is not known.") . $contactRealex
				);
			case 'UNABLE TO AUTH':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The authorization at the bank failed.") . $contactRealex
				);
		}
	
		// We map here some messages, which are not clear and must be more explained.
		switch($returnCode) {
			case '102':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The bank refuse the transaction. Reason: !reason", array('!reason' => $message)) . $contactRealex
				);
			case '103':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Your card seems to reported as stolen or lost."),
					Customweb_I18n_Translation::__("The customers's card is reported to be stolen or lost.")
				);
			case '104':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The authorization could not be done.")
				);
			case '106':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("The issuer number for your Switch Card seems to be wrong."),
					Customweb_I18n_Translation::__("The authorization failed. May be the Switch Card isssue number is wrong.") . $contactRealex
				);
			case '107':
				return new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("You are not allowed to do this transaction.") . $contactMerchant,
					Customweb_I18n_Translation::__("Your fraud settings prevents the acceptance of this transaction.")
				);
			case '108':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("You trying to use live cards on the test system. Please use only test cards.")
				);
			case '109':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$outageMessage,
					Customweb_I18n_Translation::__("The bank has a scheduled maintenance outage.") . $outagePersists
				);
			case '200':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$outageMessage,
					Customweb_I18n_Translation::__("The bank reports an unkown error.") . $contactRealex
				);
			case '202':
			case '205':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$outageMessage,
					Customweb_I18n_Translation::__("There occours a network outage at the bank side.") . $outagePersists
				);
			case '666':
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__('The account has been deactivated by Realex.') . $contactRealex
				);
		}
	
		// Handle all other messages by their category:
		$categoryCode = $returnCode = substr($returnCode, 0, 1);
		switch($categoryCode) {
			case Customweb_Realex_IConstant::STATUS_TRANSACTION_FAILED:
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__("The transaction failed due to an unkown reason.") . $contactRealex
				);
			case Customweb_Realex_IConstant::STATUS_BANK_SYSTEM_ERROR:
				return new Customweb_Payment_Authorization_ErrorMessage(
					$outageMessage,
					Customweb_I18n_Translation::__("The bank system seems to be down to an unkown reason.") . $outagePersists
				);
			case Customweb_Realex_IConstant::STATUS_REALEX_PAYMENT_SYSTEM_ERROR:
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__(
						"There seems to be an error of your Realex account. Details: '!details'.",
						array('!details' => (string)$message)
					) . $contactRealex
				);
			case Customweb_Realex_IConstant::STATUS_INCORRECT_XML:
				return new Customweb_Payment_Authorization_ErrorMessage(
					$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__(
						"There seems to be an error in the implementation of the payment module. Details: '!details'.",
						array('!details' => (string)$message)
					)
				);
			default:
				return new Customweb_Payment_Authorization_ErrorMessage(
				$generalUserMessage . $contactMerchant,
					Customweb_I18n_Translation::__(
						"An unkown error occurs. Details: '!details'.",
						array('!details' => (string)$message)
					)
				);
		}
	}
	
	
	
}