<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Landingpage\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope     as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\LandingPage\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\LandingPage\Source\Type as TypeOptions;
use MageWorx\SeoXTemplates\Model\Template\LandingPage as LandingPageTemplate;
use MageWorx\SeoXTemplates\Helper\Comment\LandingPage as HelperComment;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @var ScopeOptions
     */
    protected $scopeOptions;

    /**
     * @var IsUseCronOptions
     */
    protected $isUseCronOptions;

    /**
     *
     * @var AssignTypeOptions
     */
    protected $assignTypeOptions;

    /**
     *
     * @var TypeOptions
     */
    protected $typeOptions;

    /**
     *
     * @var HelperComment
     */
    protected $helperComment;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * Main constructor.
     * @param Store $store
     * @param TypeOptions $typeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param ScopeOptions $scopeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperComment $helperComment
     * @param HelperStore $helperStore
     * @param array $data
     */
    public function __construct(
        Store $store,
        TypeOptions $typeOptions,
        IsUseCronOptions $isUseCronOptions,
        ScopeOptions $scopeOptions,
        AssignTypeOptions $assignTypeOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperComment $helperComment,
        HelperStore $helperStore,
        array $data = []
    ) {
        $this->registry              = $registry;
        $this->store                 = $store;
        $this->scopeOptions          = $scopeOptions;
        $this->isUseCronOptions      = $isUseCronOptions;
        $this->assignTypeOptions     = $assignTypeOptions;
        $this->typeOptions           = $typeOptions;
        $this->helperComment         = $helperComment;
        $this->helperStore           = $helperStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\LandingPage\ $template */
        $template = $this->registry->registry('mageworx_seoxtemplates_template');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($template->getId()) {
            $fieldset->addField(
                'template_id',
                'hidden',
                ['name' => 'template_id']
            );
        }

        $fieldset->addField(
            'type_id',
            'hidden',
            [
                'name'      => 'type_id',
                'value'     => $this->getTemplateTypeId()
            ]
        );

        $fieldset->addField(
            'store_id',
            'hidden',
            [
                'name'      => 'store_id',
                'value'     => $this->getTemplateStoreId()
            ]
        );

        $fieldset->addField(
            'is_single_store_mode',
            'hidden',
            [
                'name'  => 'is_single_store_mode',
                'value' => $this->getIsSingleStoreMode()
            ]
        );

        $fieldset->addField('assign_type', 'radios', [
          'label'              => 'Assign Type',
          'name'               => 'assign_type',
          'values'             => $this->getAssignTypes(),
          'disabled'           => false,
          'readonly'           => false,
          'after_element_html' => '<br><small>' . __('See the changed tab in the tab list') . '</small>',
        ]);

        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Name'),
                'title'     => __('Name'),
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'code',
            'text',
            [
                'name'      => 'code',
                'label'     => __('Template Rule'),
                'title'     => __('Template Rule'),
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'scope',
            'select',
            [
                'name'      => 'scope',
                'label'     => __('Apply For'),
                'title'     => __('Apply For'),
                'required'  => true,
                'options'   => $this->scopeOptions->toArray()
            ]
        );

         $fieldset->addField(
             'is_use_cron',
             'select',
             [
                'name'      => 'is_use_cron',
                'label'     => __('Apply By Cron'),
                'title'     => __('Apply By Cron'),
                'required'  => true,
                 'options'   => $this->isUseCronOptions->toArray(),
                 'after_element_html' => $this->helperComment->getComments($this->getTemplateTypeId()),

             ]
         );

        $templateData = $this->_session->getData('mageworx_seoxtemplates_template_data', true);

        if ($templateData) {
            $template->addData($templateData);
        } else {
            if (!$template->getId()) {
                $template->addData($template->getDefaultValuesForEdit());
            }
        }

        $form->addValues($template->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Landing Page Template');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve legend text
     *
     * @return string
     */
    protected function getLegendText()
    {
        $storeId        = $this->getTemplateStoreId();
        $storeViewName  = $this->store->getStoreName($storeId);

        $templateTypes  = $this->typeOptions->toArray();
        $templateName   = $templateTypes[$this->getTemplateTypeId()];

        if (!$storeId && $this->getIsSingleStoreMode()) {
            return __('Edit "%1" Template for Single-Store Mode', $templateName);
        }

        if ($storeId == 'default' || $this->getLandingPageTemplate()->getUseForDefaultValue()) {
            return __('Edit "%1" Template for Default Values', $templateName);
        }

        if (!$storeId) {
            return __('Edit "%1" Template for All Store Views', $templateName);
        }

        return __('Edit "%1" Template for "%2" Store View', $templateName, $storeViewName);
    }

    /**
     *
     * @return int
     */
    protected function getTemplateStoreId()
    {
        return $this->getLandingPageTemplate()->getStoreId();
    }

    /**
     *
     * @return int
     */
    protected function getTemplateTypeId()
    {
        return $this->getLandingPageTemplate()->getTypeId();
    }

    /**
     *
     * @return int
     */
    protected function getIsSingleStoreMode()
    {
        if (is_null($this->getRequest()->getParam('is_new'))) {
            return $this->getLandingPageTemplate()->getIsSingleStoreMode();
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            return HelperStore::SINGLE_STORE_MODE_ENABLED;
        }

        return HelperStore::SINGLE_STORE_MODE_DISABLED;
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template\LandingPage
     */
    protected function getLandingPageTemplate()
    {
        return $this->registry->registry('mageworx_seoxtemplates_template');
    }

    /**
     * Retrieve filtered by same template type assign options
     *
     * @return array
     */
    protected function getAssignTypes()
    {
        $options = $this->assignTypeOptions->toOptionArray();

        if ($this->getLandingPageTemplate()->getDuplicateTemplateAssignedForAll()) {
            foreach ($options as $key => $option) {
                if ($option['value'] == \MageWorx\SeoXTemplates\Model\Template\LandingPage::ASSIGN_ALL_ITEMS) {
                    unset($options[$key]);
                }
            }
        }
        return $options;
    }

    /**
     * Add JS tab switcher in accordin template's assigned type
     *
     * @return string
     */
    protected function _toHtml()
    {

        return parent::_toHtml() .
        "<script>
             require([
            'jquery'
        ], function($) {
            $('#template_assign_type1').on('change', function() {

                if ($('#template_assign_type1:checked')) {
                    $('#template_landingpage_tabs_landingpages').parent().hide();
                }
            });

            $('#template_assign_type2').on('change', function() {

                if ($('#template_assign_type2:checked')) {
                    $('#template_landingpage_tabs_landingpages').parent().show();
                }
            });
        });
        </script>";
    }

    /**
     * @param LandingPageTemplate $type
     * Return comments for landing page template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function getComments($type)
    {
        $comment = '<small>';
        switch ($type) {
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_TITLE:
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_DESCRIPTION:
            case LandingPageTemplate::TYPE_LANDING_PAGE_META_KEYWORDS:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_1:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_2:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_3:
            case LandingPageTemplate::TYPE_LANDING_PAGE_TEXT_4:
                $comment .= '<br><p>' . $this->getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->getLnPersonalFiltersComment();
                $comment .= '<br><p>' . $this->getAdditionalComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            case LandingPageTemplate::TYPE_LANDING_PAGE_HEADER:
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Landing Page Template Type'));
        }
        return $comment.'</small>';
    }

    /**
     * Return comment for filter_all
     *
     * @return string
     */
    protected function getLnAllFiltersComment()
    {
        $string = '<b>[filter_all]</b> - ' . __('inserts all chosen attributes of LN on the landing page.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . " <b>" . '[landing_page][ – parameters: {filter_all}]' . "</b>";
        $string .= " - " . __('If "color", "occasion", and "shoe size" attributes are chosen, on the frontend you will see:');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('"Shoes – parameters: Color Red, Occasion Casual, Shoe Size 6.5"');
        $string .= " - " . __('If no attributes are chosen, you will see: "Shoes".');

        return $string;
    }

    /**
     * Return comment for personal filters
     *
     * @return string
     */
    protected function getLnPersonalFiltersComment()
    {
        $string = '<b>[filter_<i>attribute_code</i>]</b> - ' . __('insert attribute value if exists.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . ' <b>[landing_page][ in {filter_color}]</b>';
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Will translate to "Shoes in Color Red" on the frontend.');

        return $string;
    }

    /**
     * Return additional comment
     *
     * @return string
     */
    protected function getAdditionalComment()
    {
        $note = '<p><font color = "#ea7601">';
        $note .= __('Note: The variables [%s] and [%s] will be replaced by their values Only on the front-end.', 'filter_all', "filter_<i>attribute_code</i>");
        $note .= ' ' . __('So, in the backend you will still see [%s] and [%s].', 'filter_all', "filter_<i>attribute_code</i>");

        $note .= '</font>';

        return $note;
    }

    /**
     * Return comment for randomizer
     *
     * @return string
     */
    protected function getRandomizeComment()
    {
        return '<p>'. __('Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word.').'<br>'.__('
        Also randomizers can be used within other template variables, ex: [-parameters:||-filters: {filter_all}]. Number of randomizers blocks is not limited within the template.').'<br>';
    }
}
