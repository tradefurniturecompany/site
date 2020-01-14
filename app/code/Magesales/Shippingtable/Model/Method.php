<?php
namespace Magesales\Shippingtable\Model;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magesales\Shippingtable\Model\MethodFactory;

class Method extends \Magento\Framework\Model\AbstractModel
{
	protected $_methodFactory;
	
    public function __construct(Context $context,Registry $registry,MethodFactory $methodFactory)
	{
		$this->_methodFactory = $methodFactory;
        parent::__construct($context, $registry);
    }
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('Magesales\Shippingtable\Model\ResourceModel\Method');
    }
    
    public function massChangeStatus ($ids, $status)
	{
        foreach ($ids as $id)
		{
			$model = $this->_methodFactory->create()->load($id);
            $model->setIsActive($status);
            $model->save();
        }
        return $this;
    }

    public function addComment($html)
    {
        preg_match_all('@<label for="s_method_shippingtable_shippingtable(.+?)">.+?label>@si', $html, $matches);
        if (!empty($matches[0])) {
            $hashMethods = $this->_methodFactory->getCollection()->toOptionHash();
            foreach ($matches[0] as $key => $value) {
                $methodId = $matches[1][$key];
                $to[] = $matches[0][$key] . '<div>' . $hashMethods[$methodId] . '</div>';
            }

            $newHtml = str_replace($matches[0], $to, $html);
            return $newHtml;
        }

        return $html;
    }
}