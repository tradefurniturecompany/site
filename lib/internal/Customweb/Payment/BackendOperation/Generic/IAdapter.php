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
 * This adapter allows the implementation of backend operations.
 * 
 * @author Thomas Hunziker
 *
 */
interface Customweb_Payment_BackendOperation_IAdapter {
	
	/**
	 * This method returns a list of operations supported by this adapter.
	 * 
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return Customweb_Payment_BackendOperation_IOperation[]
	 */
	public function getOperations(Customweb_Payment_Authorization_ITransaction $transaction);
	
	/**
	 * This method returns a list of form elements. The result of the submitted
	 * form is the input for the exeuction of the operation.
	 * 
	 * @param string $operationIdentifier
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param array $formDataOfFailedExecution In case the execution failes with an exception, this array should contain the previous entered user data.
	 * @param boolean $willProvideModifications Whether the caller will provide an order modification or not.
	 * @return Customweb_Form_IElement[] A list of form elements.
	 */
	public function getFormElementsByOperation($operationIdentifier, Customweb_Payment_Authorization_ITransaction $transaction, array $formDataOfFailedExecution, $willProvideModifications);
	
	/**
	 * The execution of the operation may fail. In case the operation fails, the method throws 
	 * an exception. In case of an exception the form should be shown again to the user including 
	 * the exception message.
	 * 
	 * In case no exception is thrown, the operation was successfull and the transcation can be persisted. 
	 * The transaction state may be changed, hence the order status must be updated after teh execution 
	 * of the operation.
	 * 
	 * The caller may provide a new order context, which reflects the changes done aligned with this operation. This is
	 * possible, when the operation permits that by the method canAcceptOrderModifications() and if the caller
	 * indicates that when he calls the self::getFormElementsByOperation() method.
	 * 
	 * @param string $operationIdentifier
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param array $formData
	 * @param Customweb_Payment_Authorization_IOrderContext $orderModifications The new order context with the modifications.
	 * @throws Exception
	 * @return void
	 */
	public function executeOperation($operationIdentifier, Customweb_Payment_Authorization_ITransaction $transaction, array $formData, $orderModifications);
	
	
}