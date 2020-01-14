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

class Clear extends \Magento\Config\Block\System\Config\Form\Field
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
       
        $url = $this->getUrl("apptrian_imageoptimizer/optimizer/clear");
        
        $confirmText = __('Are you sure you want to do this?');
        
        $data = [
            'class'   => 'apptrian-imageoptimizer-admin-button-clear',
            'label'   => __('Clear Index'),
            'onclick' => "confirmSetLocation('".$confirmText."', '".$url."')",
        ];
        
        $html = $buttonBlock->setData($data)->toHtml();
        
        return $html;
    }
}
