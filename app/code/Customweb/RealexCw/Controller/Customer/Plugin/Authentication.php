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

namespace Customweb\RealexCw\Controller\Customer\Plugin;

use Magento\Framework\App\RequestInterface;

class Authentication
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerUrl = $customerUrl;
        $this->_customerSession = $customerSession;
    }

    /**
     * Authenticate user
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param RequestInterface $request
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, RequestInterface $request)
    {
        $loginUrl = $this->_customerUrl->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $subject->getActionFlag()->set('', $subject::FLAG_NO_DISPATCH, true);
        }
    }
}