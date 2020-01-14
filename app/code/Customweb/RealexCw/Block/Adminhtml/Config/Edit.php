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

class Edit extends \Magento\Backend\Block\Widget
{
    /**
     * Form block class name
     *
     * @var string
     */
    protected $_formBlockName = 'Customweb\RealexCw\Block\Adminhtml\Config\Form';

    /**
     * Block template File
     *
     * @var string
     */
    protected $_template = 'Customweb_RealexCw::realexcw/config/edit.phtml';
    
    /**
     * @var \Customweb\RealexCw\Model\Config\Structure
     */
    protected $_formStructure;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Structure $formStructure
     * @param array $data
     */
    public function __construct(
        	\Magento\Backend\Block\Template\Context $context,
    		\Customweb\RealexCw\Model\Config\Structure $formStructure,
        	array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_formStructure = $formStructure;
    }

    /**
     * Prepare layout object
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
    	$form = $this->_formStructure->getForm($this->getRequest()->getParam('form'));
    	$this->setTitle($form->getTitle());

    	foreach ($form->getButtons() as $button) {
	        $this->getToolbar()->addChild(
	            $button->getMachineName(),
	            'Magento\Backend\Block\Widget\Button',
	            [
	                'id' => $button->getMachineName(),
	                'label' => $button->getTitle(),
	                'class' => ($button->getType() == \Customweb_Form_IButton::TYPE_SUCCESS ? 'primary' : ''),
	            	'onclick' => 'submitBackendForm(\'' . $form->getId() . '\', \'' . $button->getMachineName() . '\', '.($button->isJSValidationExecuted() ? 'true' : 'false').');'
	            ]
	        );
    	}
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }
}
