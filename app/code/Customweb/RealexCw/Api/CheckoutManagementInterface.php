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
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 *
 */

namespace Customweb\RealexCw\Api;

/**
 * Checkout management interface.
 * @api
 */
interface CheckoutManagementInterface
{
	/**
	 * Authorizes the transaction.
	 *
	 * @param int $orderId
	 * @param \Customweb\RealexCw\Api\Data\AuthorizationFormFieldInterface[] $formValues
	 * @return \Customweb\RealexCw\Api\Data\AuthorizationDataInterface
	 */
	public function authorize($orderId, array $formValues = null);

	/**
	 * Authorizes the transaction.
	 *
	 * @param string $cartId
	 * @param int $orderId
	 * @param \Customweb\RealexCw\Api\Data\AuthorizationFormFieldInterface[] $formValues
	 * @return \Customweb\RealexCw\Api\Data\AuthorizationDataInterface
	 */
	public function guestAuthorize($cartId, $orderId, array $formValues = null);

	/**
	 * Gets the payment form.
	 *
	 * @param string $cartId
	 * @param string $paymentMethod
	 * @param int $alias
	 * @return \Customweb\RealexCw\Api\Data\PaymentFormInterface
	 */
    public function getPaymentForm($cartId, $paymentMethod, $alias = null);

    /**
     * Gets the payment form for guest customers.
     *
     * @param string $cartId
     * @param string $paymentMethod
     * @param int $alias
     * @return \Customweb\RealexCw\Api\Data\PaymentFormInterface
     */
    public function getGuestPaymentForm($cartId, $paymentMethod, $alias = null);

}