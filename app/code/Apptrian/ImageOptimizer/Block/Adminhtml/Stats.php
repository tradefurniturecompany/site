<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
 
namespace Apptrian\ImageOptimizer\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Stats extends \Magento\Config\Block\System\Config\Form\Field
{
    
    /**
     * @var \Apptrian\ImageOptimizer\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Apptrian\ImageOptimizer\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Apptrian\ImageOptimizer\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $element = null;
        
        $r         = [];
        $indexed   = 0;
        $optimized = 0;
        
        try {
            $r = $this->helper->getFileCount();
            $indexed   = $r['indexed'];
            $optimized = $r['optimized'];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return '<div class="apptrian-imageoptimizer-bar-wrapper">
            <div class="apptrian-imageoptimizer-bar-outer">
            <div class="apptrian-imageoptimizer-bar-inner" style="width: 0;">
            </div><div class="apptrian-imageoptimizer-bar-text">
            <span style="color: #f00;">' . $message . '</span></div>
            </div></div>';
        }
        
        // Fix for division by zero possibility
        if ($indexed == 0) {
            $percent = 0;
        } else {
            $percent = round((100 * $optimized) / $indexed, 2);
        }
        
        $html = '<div class="apptrian-imageoptimizer-bar-wrapper">
        <div class="apptrian-imageoptimizer-bar-outer">
        <div class="apptrian-imageoptimizer-bar-inner" style="width:'
        . $percent .'%;"></div>
        <div class="apptrian-imageoptimizer-bar-text"><span>' . $percent
        . '% ' . sprintf(__('(%s of %s files)'), $optimized, $indexed)
        . '</span></div>
        </div></div>';
        
        return $html;
    }
}
