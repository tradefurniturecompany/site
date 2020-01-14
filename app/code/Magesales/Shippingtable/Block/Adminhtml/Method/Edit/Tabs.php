<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Method\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
 
class Tabs extends WidgetTabs
{
     /**
     * @var Registry
     */
    protected $registry;

    /**
     * {@inheritdoc}
     * @param Registry         $registry
     * @param Context          $context
     * @param EncoderInterface $jsonEncoder
     * @param Session          $authSession
     */
    public function __construct(
        Registry $registry,
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession
    ) {
        $this->registry = $registry;

        parent::__construct($context, $jsonEncoder, $authSession);
    }
	
	public function _construct()
    {
        parent::_construct();
        $this->setId('methodTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Method Methods'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general',[
			'label'   => __('General'),
            'title'   => __('General'),
            'content' => $this->getLayout()->createBlock('\Magesales\Shippingtable\Block\Adminhtml\Method\Edit\Tab\General')->toHtml(),
        ]);
		
		//$this->addTab('stores',[
//			'label'   => __('Stores & Customer Groups'),
//            'title'   => __('Stores & Customer Groups'),
//            'content' => $this->getLayout()->createBlock('\Magesales\Shippingtable\Block\Adminhtml\Method\Edit\Tab\Stores')->toHtml(),
//        ]);
		
		$this->addTab('import',[
			'label'   => __('Import'),
            'title'   => __('Import'),
            'content' => $this->getLayout()->createBlock('\Magesales\Shippingtable\Block\Adminhtml\Method\Edit\Tab\Import')->toHtml(),
        ]);
        
        $this->addTab('rates', [
            'label'     => __('Methods and Rates'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('shippingtable/rate/index', ['_current' => true]),
        ]);
    
        $this->_updateActiveTab();    
    
        return parent::_beforeToHtml();
    }
    
    protected function _updateActiveTab()
    {
    	$tabId = $this->getRequest()->getParam('tab');
    	if ($tabId) {
    		$tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
    		if ($tabId) {
    			$this->setActiveTab($tabId);
    		}
    	}
    	else {
    	   $this->setActiveTab('main'); 
    	}
    }     
 
    
}