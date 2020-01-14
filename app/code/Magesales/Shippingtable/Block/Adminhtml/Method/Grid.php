<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Method;
use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;

class Grid extends Extended
{
	/**
     * @var RuleCollectionFactory
     */
	protected $_methodCollection;
	protected $_helper;

    /**
     * {@inheritdoc}
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param Context               $context
     * @param BackendHelper         $backendHelper
     */
    public function __construct(
        \Magesales\Shippingtable\Model\ResourceModel\Method\CollectionFactory $methodCollection,
		\Magesales\Shippingtable\Helper\Data $helper,
        Context $context,
		BackendHelper $backendHelper		
    ) {
        $this->_methodCollection = $methodCollection;
		$this->_helper = $helper;

        parent::__construct($context, $backendHelper);
    }
  
  	public function _construct()
  	{
		parent::_construct();
      	$this->setId('methodGrid');
      	$this->setDefaultSort('pos');
  	}

  	protected function _prepareCollection()
  	{
      	$collection = $this->_methodCollection->create();
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
    	$hlp =  $this->_helper;
    	$this->addColumn('method_id', [
     		'header'    => __('ID'),
      		'align'     => 'right',
      		'width'     => '50px',
      		'index'     => 'method_id',
    	]);

		$this->addColumn('name', [
			'header'    => __('Name'),
			'index'     => 'name',
		]);
		
		$this->addColumn('pos', [
			'header'    => __('Priority'),
			'index'     => 'pos',
		]);    
		
		$this->addColumn('is_active', [
			'header'    => __('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'renderer'	=> '\Magesales\Shippingtable\Block\Adminhtml\Method\Grid\Renderer\Color',
			'index'     => 'is_active',
			'type'      => 'options',
			'options'   => $hlp->getStatuses(),
		]);    
		
		return parent::_prepareColumns();
  	}

	public function getRowUrl($row)
	{
		return $this->getUrl('shippingtable/method/edit', ['id' => $row->getId()]);
	}
  
	protected function _prepareMassaction()
	{
	  	$this->setMassactionIdField('method_id');
	  	$this->getMassactionBlock()->setFormFieldName('methods');
	  
	  	$actions = [
			'massActivate'   => 'Activate',
		  	'massInactivate' => 'Inactivate',
		  	'massDelete'     => 'Delete',
	  	];
	  
	  	foreach ($actions as $code => $label){
		  	$this->getMassactionBlock()->addItem($code, [
			   'label'    => __($label),
			   'url'      => $this->getUrl('*/*/' . $code),
			   'confirm'  => ($code == 'massDelete' ? __('Are you sure?') : null),
		  	]);        
	  	}
	  	return $this; 
	}
}