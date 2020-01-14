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

class About extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Apptrian\ImageOptimizer\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \Apptrian\ImageOptimizer\Helper\Data $helper
     */
    public function __construct(\Apptrian\ImageOptimizer\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element  = null;
        $version  = $this->helper->getExtensionVersion();
        $logopath = 'https://www.apptrian.com/media/apptrian.gif';
        $html     = <<<HTML
<div style="background: url('$logopath') no-repeat scroll 15px 15px #f8f8f8; 
border:1px solid #ccc; min-height:100px; margin:5px 0; 
padding:15px 15px 15px 140px;">
<p>
<strong>Apptrian Image Optimizer Extension v$version</strong><br /><br />
Optimize your images, reduce their file size without losing image quality and 
speed up your site.
</p>
</div>
HTML;
        return $html;
    }
}
