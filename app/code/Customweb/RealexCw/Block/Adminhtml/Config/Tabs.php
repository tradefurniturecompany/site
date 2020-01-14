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

namespace Customweb\RealexCw\Block\Adminhtml\Config;

class Tabs extends \Magento\Backend\Block\Widget
{
    /**
     * Forms
     *
     * @var array
     */
    protected $_forms;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Customweb_RealexCw::realexcw/config/tabs.phtml';
    
    /**
     * Currently selected form id
     *
     * @var string
     */
    protected $_currentFormMachineName;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Customweb\RealexCw\Model\DependencyContainer $container
     * @param array $data
     */
    public function __construct(
			\Magento\Backend\Block\Template\Context $context,
    		\Customweb\RealexCw\Model\Config\Structure $formStructure,
			array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->_forms = $formStructure->getForms();

        $this->setId('realexcw_config_tabs');
        $this->setTitle(__('Realex'));
        $this->_currentFormMachineName = $this->getRequest()->getParam('form');
    }

    /**
     * Get all forms
     *
     * @return array
     */
    public function getForms()
    {
    	return $this->_forms;
    }

    /**
     * Retrieve section url by form
     *
     * @param \Customweb_Payment_BackendOperation_IForm $form
     * @return string
     */
    public function getFormUrl(\Customweb_Payment_BackendOperation_IForm $form)
    {
        return $this->getUrl('*/*/*', ['_current' => true, 'form' => $form->getMachineName()]);
    }

    /**
     * Check whether form should be displayed as active
     *
     * @param \Customweb_Payment_BackendOperation_IForm $form
     * @return bool
     */
    public function isFormActive(\Customweb_Payment_BackendOperation_IForm $form)
    {
        return $form->getMachineName() == $this->_currentFormMachineName;
    }
}
