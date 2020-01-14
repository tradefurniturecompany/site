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

namespace Customweb\RealexCw\Model\ResourceModel\Grid;

class PaymentMethodList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     */
    public function __construct(\Magento\Payment\Helper\Data $paymentData)
    {
        $this->_paymentData = $paymentData;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$methods = [];
    	foreach ($this->_paymentData->getPaymentMethods() as $code => $data) {
    		if (strpos($code, 'realexcw_') === 0) {
    			if (isset($data['title'])) {
    				$methods[$code] = $data['title'];
    			} else {
    				$methods[$code] = $this->getMethodInstance($code)->getConfigData('title');
    			}
    		}
    	}
    	asort($methods);
        return $methods;
    }
}