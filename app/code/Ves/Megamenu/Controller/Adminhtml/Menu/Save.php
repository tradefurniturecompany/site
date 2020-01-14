<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Controller\Adminhtml\Menu;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Ves_Megamenu::menu_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if(isset($data['cms_page'])) {
                unset($data['cms_page']);
            }
            $model = $this->_objectManager->create('Ves\Megamenu\Model\Menu');
            $id = $this->getRequest()->getParam('menu_id');
            if ($id) {
                $model->load($id);
            }
            if($this->getRequest()->getParam("revert_previous")){
                $data['revert_previous'] = $this->getRequest()->getParam("revert_previous");
            }
            if($this->getRequest()->getParam("revert_next")){
                $data['revert_next'] = $this->getRequest()->getParam("revert_next");
            }
            $model->setData($data);
            try {
                $model->save();
                if(isset($data['revert_previous']) || isset($data['revert_next'])) {
                    if(isset($data['revert_previous'])){
                        $version = $data['revert_previous'];
                    }
                    if(isset($data['revert_next'])){
                        $version = $data['revert_next'];
                    }
                    $this->messageManager->addSuccess(__('You reverted the menu id %1, version #%2', $id, $version));
                } else {
                    $this->messageManager->addSuccess(__('You saved this menu.'));
                }
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getId(), '_current' => true]);
                }
                if(!$this->getRequest()->getParam("duplicate")){
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the menu.'));
            }

            if($this->getRequest()->getParam("duplicate")){
                unset($data['menu_id']);
                $data['alias'] = $data['alias'].time();
                $data['duplicate'] = true;
                $menu = $this->_objectManager->create('Ves\Megamenu\Model\Menu');
                $menu->setData($data);
                try{
                    $menu->save();
                    $this->messageManager->addSuccess(__('You duplicated this menu.'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while duplicating the menu.'));
                }
            }
            //$this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['menu_id' => $this->getRequest()->getParam('menu_id')]);
        }
    }
}