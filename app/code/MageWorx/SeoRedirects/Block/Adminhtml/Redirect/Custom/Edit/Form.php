<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Block\Adminhtml\Redirect\Custom\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoRedirects\Controller\RegistryConstants;
use MageWorx\SeoAll\Model\Source\Product\Attribute as CategoryFilterOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\Status as StatusOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\RedirectType as RedirectCodeOptions;
use MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity as RequestEntityTypeOptions;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser as CategoryChooser;

class Form extends GenericForm
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @var CategoryFilterOptions
     */
    protected $categoryFilterOptions;

    /**
     * @var StatusOptions
     */
    protected $statusOptions;

    /**
     * @var RedirectCodeOptions
     */
    protected $redirectCodeOptions;

    /**
     * @var RequestEntityTypeOptions
     */
    protected $requestEntityTypeOptions;

    /**
     * Form constructor.
     *
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param CategoryFilterOptions $categoryFilterOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param StatusOptions $statusOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        CategoryFilterOptions $categoryFilterOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        StatusOptions $statusOptions,
        RedirectCodeOptions $redirectCodeOptions,
        RequestEntityTypeOptions $requestEntityTypeOptions,
        CategoryChooser $categoryChooser,
        array $data = []
    ) {
        $this->systemStore              = $systemStore;
        $this->categoryFilterOptions    = $categoryFilterOptions;
        $this->statusOptions            = $statusOptions;
        $this->redirectCodeOptions      = $redirectCodeOptions;
        $this->requestEntityTypeOptions = $requestEntityTypeOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
