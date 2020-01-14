<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Controller\Adminhtml\Field;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mageplaza\Osc\Helper\Data as OscHelper;

/**
 * Class Save
 * @package Mageplaza\Osc\Controller\Adminhtml\Field
 */
class Save extends Action
{
    /**
     * @var Config
     */
    protected $resourceConfig;

    /**
     * Application config
     *
     * @var ScopeConfigInterface
     */
    protected $_appConfig;

    /**
     * @param Context $context
     * @param Config $resourceConfig
     * @param ReinitableConfigInterface $config
     */
    public function __construct(
        Context $context,
        Config $resourceConfig,
        ReinitableConfigInterface $config
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->_appConfig = $config;

        parent::__construct($context);
    }

    /**
     * save position to config
     */
    public function execute()
    {
        $result = [
            'success' => false,
            'message' => __('Error during save field position.')
        ];

        $fields = $this->getRequest()->getParam('fields', false);
        if ($fields) {
            $this->resourceConfig->saveConfig(
                OscHelper::SORTED_FIELD_POSITION,
                $fields,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                0
            );

            // re-init configuration
            $this->_appConfig->reinit();

            $result['success'] = true;
            $result['message'] = __('All fields have been saved.');
        }

        $this->getResponse()->setBody(OscHelper::jsonEncode($result));
    }
}
