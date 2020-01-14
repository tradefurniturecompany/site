<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
 
namespace Apptrian\ImageOptimizer\Block\Adminhtml\Button;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Optimize extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $element = null;
        
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button');
       
        $url = $this->getUrl("apptrian_imageoptimizer/optimizer/optimize");
            
        $data = [
            'class'   => 'apptrian-imageoptimizer-admin-button-optimize',
            'label'   => __('Start Optimization Process'),
            'onclick' => "setLocation('" . $url . "')",
        ];
        
        $html = $buttonBlock->setData($data)->toHtml();
        
        return $html;
    }
}
