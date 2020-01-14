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

namespace Customweb\RealexCw\Model\Authorization\Method\Context;

class Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
    		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param \Customweb\RealexCw\Model\Payment\Method\AbstractMethod $paymentMethod
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $alias
     * @return \Customweb\RealexCw\Model\Authorization\Method\Context\Quote
     */
    public function createQuote(\Customweb\RealexCw\Model\Payment\Method\AbstractMethod $paymentMethod = null, \Magento\Quote\Model\Quote $quote = null, $alias = null)
    {
        return $this->_objectManager->create('Customweb\\RealexCw\\Model\\Authorization\\Method\\Context\\Quote', ['paymentMethod' => $paymentMethod, 'quote' => $quote, 'alias' => $alias]);
    }

    /**
     * @param \Magento\Sales\Model\Order $paymentMethod
     * @return \Customweb\RealexCw\Model\Authorization\Method\Context\Order
     */
    public function createOrder(\Magento\Sales\Model\Order $order = null)
    {
    	return $this->_objectManager->create('Customweb\\RealexCw\\Model\\Authorization\\Method\\Context\\Order', ['order' => $order]);
    }

    /**
     * @param \Customweb\RealexCw\Model\Authorization\Transaction
     * @return \Customweb\RealexCw\Model\Authorization\Method\Context\Transaction
     */
    public function createTransaction(\Customweb\RealexCw\Model\Authorization\Transaction $transaction = null, \Magento\Sales\Model\Order $order = null, array $parameters = null)
    {
    	return $this->_objectManager->create('Customweb\\RealexCw\\Model\\Authorization\\Method\\Context\\Transaction', ['transaction' => $transaction, 'order' => $order, 'parameters' => $parameters]);
    }
}
