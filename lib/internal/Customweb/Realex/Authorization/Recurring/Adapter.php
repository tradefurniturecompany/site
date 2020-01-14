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
 * This Class is the entry point for the Realex Payments RECURRING payments and is called by the payment_api.
 *
 * @author Mathis Kappeler
 * @Bean
 *
 */
final class Customweb_Realex_Authorization_Recurring_Adapter extends Customweb_Realex_Authorization_AbstractAdapter implements Customweb_Payment_Authorization_Recurring_IAdapter{

	public function getAuthorizationMethodName() {
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function getAdapterPriority() {
		return 1001;
	}

	public function isPaymentMethodSupportingRecurring(Customweb_Payment_Authorization_IPaymentMethod $paymentMethod){
		return true;
	}

	public function createTransaction(Customweb_Payment_Authorization_Recurring_ITransactionContext $transactionContext){
		$transaction = new Customweb_Realex_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod(self::AUTHORIZATION_METHOD_NAME);
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		return $transaction;
	}

	public function process(Customweb_Payment_Authorization_ITransaction $transaction){
		try {
			$this->setTransaction($transaction);

			$processor = new Customweb_Realex_Authorization_RealVault_XmlAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
			$processor->process();

			if ($this->getTransaction()->isAuthorizationFailed()) {
				$errorMessage = current( $this->getTransaction()->getErrorMessages());
				$message = null;
				if ($errorMessage instanceof Customweb_Payment_Authorization_IErrorMessage) {
					$message = $errorMessage->getBackendMessage();
				}
				else {
					$message = (string)$errorMessage;
				}
				throw new Customweb_Payment_Exception_RecurringPaymentErrorException($message);
			}
		}
		catch(Customweb_Payment_Exception_RecurringPaymentErrorException $e) {
			throw $e;
		}
		catch(Exception $e) {
			throw new Customweb_Payment_Exception_RecurringPaymentErrorException($e);
		}
	}
}
