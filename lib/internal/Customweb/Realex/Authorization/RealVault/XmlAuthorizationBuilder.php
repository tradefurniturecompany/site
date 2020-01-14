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
 * This class build the XML to authorize a transaction with the RealVault.
 * 
 * @author Mathis Kappeler
 */
final class Customweb_Realex_Authorization_RealVault_XmlAuthorizationBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder {

	private $payerReference = null;
	
	/**
	 * @var Customweb_Realex_Authorization_Transaction
	 */
	private $referenceTransaction = null;
	
	private $sequence = 'subsequent';
	
	public function __construct(Customweb_Realex_Authorization_Transaction $transaction, Customweb_Realex_Configuration $configuration, Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($transaction, $configuration, $container);
		
		
		$transactionContext = $this->getTransaction()->getTransactionContext();
		if($transactionContext instanceof Customweb_Payment_Authorization_Recurring_ITransactionContext && $transactionContext->getInitialTransaction() !== null && is_object($transactionContext->getInitialTransaction())) {
			$this->referenceTransaction = $transactionContext->getInitialTransaction();
			
		}
		else if($this->getTransaction()->getTransactionContext()->getAlias() !== null && is_object($transactionContext->getAlias())) {
			$this->referenceTransaction = $transactionContext->getAlias();
			$this->getTransaction()->setPMRef($this->referenceTransaction->getPMRef());
			// In case we create a recurring transaction based on an alias, then we need to set the PMRef on the new transaction.
			if($this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
				$this->sequence = 'first';
			}
		}
		else {
			throw new Exception("It's not possible to process a transaction with RealVault, when no alias transaction is given and no recurring transaction is given.");
		}
	}
	
	/**
	 * @return Customweb_Realex_Authorization_Transaction
	 */
	protected function getReferenceTransaction() {
		return $this->referenceTransaction;
	}
	
	protected  function getPaymentMethodReference() {
		return $this->getReferenceTransaction()->getPMRef();
	}
	
	protected function getReccuringElement() {
		if ($this->getConfiguration()->isRecurringSequenceOn()) {
			return "<recurring type='variable' sequence='" . $this->sequence . "'></recurring>";
		}
		else {
			return '';
		}
	}
	
	protected function getPaymentMethodRefElement() {
		return "<paymentmethod>" . $this->getReferenceTransaction()->getPMRef() . "</paymentmethod>";
	}
	
	
	protected function getPayerRefElement(){
		return "<payerref>" . $this->getReferenceTransaction()->getPMRef() . "</payerref>";
	}
	
	
	public function buildXml() {
		return 
			$this->getXMLHeader('receipt-in') .
			$this->getBasicElements() .
			$this->getOrderIdElement() .
			$this->getCvcElement($this->getTransaction()) . 
			$this->getAuthorizationAmountElement() .
			$this->getPayerRefElement() .
			$this->getPaymentMethodRefElement() .
			$this->getAutoSettleElement() .
			$this->getHashElement() .
			$this->getReccuringElement() .
			$this->getTSSInfoElement() .
			$this->getXMLFooter();
	}
	
	protected function getParametersToHash() {
		return array(
			$this->getTimestamp(),
			$this->getConfiguration()->getMerchantId(),
			$this->getTransaction()->getFormattedTransactionId($this->getConfiguration()),
			$this->getTransaction()->getFormattedAuthorizationAmount(),
			$this->getOrderContext()->getCurrencyCode(),
			$this->getReferenceTransaction()->getPMRef()
		);
	}
}