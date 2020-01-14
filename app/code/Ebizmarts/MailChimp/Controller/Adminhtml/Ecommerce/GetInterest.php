<?php
/**
 * mc-magento2 Magento Component
 *
 * @category Ebizmarts
 * @package mc-magento2
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 3/23/18 10:05 AM
 * @file: GetInterest.php
 */

namespace Ebizmarts\MailChimp\Controller\Adminhtml\Ecommerce;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class GetInterest extends Action
{
    /**
     * @var \Ebizmarts\MailChimp\Helper\Data
     */
    protected $_helper;
    /**
     * @var ResultFactory
     */
    protected $_resultFactory;

    /**
     * GetInterest constructor.
     * @param Context $context
     * @param \Ebizmarts\MailChimp\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Ebizmarts\MailChimp\Helper\Data $helper
    ) {

        parent::__construct($context);
        $this->_resultFactory       = $context->getResultFactory();
        $this->_helper                  = $helper;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $param = $this->getRequest()->getParams();
        $rc = [];
        $error = 0;
        if(array_key_exists('apikey',$param)&&array_key_exists('list',$param)) {
            $apiKey = $param['apikey'];
            $list  = $param['list'];
            $this->_helper->log("apikey [$apiKey] list [$list]");
            try {
                $api = $this->_helper->getApiByApiKey($apiKey);
                $result = $api->lists->interestCategory->getAll($list);
                if (is_array($result['categories'])&&count($result['categories'])) {
                    $rc = $result['categories'];
                }
            } catch(\Mailchimp_Error $e) {
                $error = 1;
            }
        }
        $resultJson = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['error' => $error, 'data' => $rc]);
        return $resultJson;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ebizmarts_MailChimp::config_mailchimp');
    }
}