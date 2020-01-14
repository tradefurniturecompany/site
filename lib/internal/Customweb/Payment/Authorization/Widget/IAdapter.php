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
 * The widget authorization is used, when the API provides some JavaScript code or HTML, which 
 * generates a HTML form for entering the data. The user of the classes with this interface must
 * not provide any button to continue the process.
 * 
 * @author Thomas Hunziker
 *
 */
interface Customweb_Payment_Authorization_Widget_IAdapter extends Customweb_Payment_Authorization_IAdapter {
	
	const AUTHORIZATION_METHOD_NAME = 'WidgetAuthorization';
	
	/**
	 * This method creates a new transaction object for the given $transactionContext.
	 * The transaction object must be supplied to all further actions. Hence
	 * the client must ensure the persistence of this object.
	 *
	 * @param Customweb_Payment_Authorization_PaymentPage_ITransactionContext $transactionContext
	 * @param Customweb_Payment_Authorization_ITransaction $failedTransaction The transaction which may be created previously
	 * @return Customweb_Payment_Authorization_ITransaction Transaction object
	 */
	public function createTransaction(Customweb_Payment_Authorization_Widget_ITransactionContext $transactionContext, $failedTransaction);
	
	/**
	 * This method returns all visible form fields for this payment method. This
	 * fields must be filled in by the customer.
	 *
	 * The $failedTransaction is a transaction that is generated previously in the order process. It can be used to provide
	 * more information on the form whats wrong with the user input. $failedTransaction can be NULL.
	 *
	 * @param Customweb_Payment_Authorization_IOrderContext $orderContext The context of the order
	 * @param Customweb_Payment_Authorization_ITransaction $aliasTransaction The alias to use with this form field. If NULL no alias is used.
	 * @param Customweb_Payment_Authorization_ITransaction $failedTransaction A previous transaction which may provide additional information.
	 * @param Customweb_payment_authorization_IPaymentCustomerContext $paymentCustomerContext The payment customer context.
	 * @return Customweb_Form_IElement[] List of visible form fields for this payment method.
	 */
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext);
	
	/**
	 * This method returns the HTML shown to the customer.
	 * 
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return string HTML content which should be shown to the customer to complete the payment.
	 */
	public function getWidgetHTML(Customweb_Payment_Authorization_ITransaction $transaction, array $formData);
	
}