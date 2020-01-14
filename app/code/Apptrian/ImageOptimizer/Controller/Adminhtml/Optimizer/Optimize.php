<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */

namespace Apptrian\ImageOptimizer\Controller\Adminhtml\Optimizer;

class Optimize extends \Magento\Backend\App\Action
{
    /**
     * @var \Apptrian\ImageOptimizer\Helper\Data
     */
    public $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Apptrian\ImageOptimizer\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Apptrian\ImageOptimizer\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        
        parent::__construct($context);
    }
    
    /**
     * Optimize action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        set_time_limit(18000);
        
        if ($this->dataHelper->isExecFunctionEnabled()) {
            try {
                $this->dataHelper->optimize();
                
                $this->messageManager->addSuccess(
                    __('Optimization operations completed successfully.')
                );
            } catch (\Exception $e) {
                $message = __('Optimization failed.');
                $this->messageManager->addError($message);
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $message = __(
                'Optimization failed because PHP exec() function is disabled.'
            );
            $this->messageManager->addError($message);
        }
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            ['section' => 'apptrian_imageoptimizer']
        );
    }
}
