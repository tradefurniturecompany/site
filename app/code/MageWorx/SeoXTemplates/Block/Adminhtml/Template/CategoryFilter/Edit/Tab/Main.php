<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\CategoryFilter\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope     as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\Category\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter\Source\Type as TypeOptions;
use MageWorx\SeoAll\Model\Source\Product\Attribute as AttributeOptions;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter as CategoryFilterTemplate;
use MageWorx\SeoXTemplates\Helper\Comment\Category as HelperComment;
use MageWorx\SeoAll\Model\Source\Product\AttributeOption;

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
     * @var AssignTypeOptions
     */
    protected $assignTypeOptions;

    /**
     *
     * @var TypeOptions
     */
    protected $typeOptions;

    /**
     * @var HelperComment
     */
    protected $helperComment;

    /**
     * @var \MageWorx\SeoAll\Model\Source\Product\AttributeOption
     */
    protected $attributeValueOptions;

    /**
     * Main constructor.
     * @param SystemStore $store
     * @param TypeOptions $typeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param ScopeOptions $scopeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param AttributeOptions $attributeOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperComment $helperComment
     * @param array $data
     */
    public function __construct(
        Store $store,
        TypeOptions $typeOptions,
        IsUseCronOptions $isUseCronOptions,
        ScopeOptions $scopeOptions,
        AssignTypeOptions $assignTypeOptions,
        AttributeOptions $attributeOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperComment $helperComment,
        AttributeOption $attributeOption,
        array $data = []
    ) {
        $this->registry              = $registry;
        $this->store                 = $store;
        $this->scopeOptions          = $scopeOptions;
        $this->isUseCronOptions      = $isUseCronOptions;
        $this->assignTypeOptions     = $assignTypeOptions;
        $this->attributeOptions      = $attributeOptions;
        $this->typeOptions           = $typeOptions;
        $this->helperComment         = $helperComment;
        $this->attributeValueOptions = $attributeOption;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template\CategoryFilter $template */
        $template = $this->registry->registry('mageworx_seoxtemplates_template');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');

        $templateData = $this->_session->getData('mageworx_seoxtemplates_template_data', true);
        if ($templateData) {
            $template->addData($templateData);
        } else {
            if (!$template->getId()) {
                $template->addData($template->getDefaultValuesForEdit());
            }
        }

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
            'attribute_id',
            'hidden',
            [
                'name'   => 'attribute_id',
                'value'  => (int)$this->getRequest()->getParam('attribute_id'),
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

        $fieldset->addField('assign_type', 'radios', [
          'label'              => 'Assign Type',
          'name'               => 'assign_type',
          'values'             => $this->getAssignTypes(),
          'disabled'           => false,
          'readonly'           => false,
          'after_element_html' => '<br><small>' . __('See the changed tab in the tab list') . '</small>',
        ]);

        $fieldset->addType('mageworx_select', '\MageWorx\SeoAll\Model\Form\Element\Select');
        $fieldset->addField(
            'attribute_option_id',
            'mageworx_select',
            [
                'name'     => 'attribute_option_id',
                'label'    => __('Attribute Value'),
                'values'   => $this->attributeValueOptions->toOptionArray($template->getAttributeId()),
                'required' => true,
            ]
        );

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
                'after_element_html' => $this->helperComment->getComments($this->getTemplateTypeId())
             ]
         );

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
        return __('Category Filter Template');
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

        if ($this->getTemplateStoreId() == 0) {
            $storeViewName = __('Each store');
        } else {
            $storeViewName = $this->store->getStoreName($this->getTemplateStoreId());
        }

        $templateTypes  = $this->typeOptions->toArray();
        $templateName   = $templateTypes[$this->getTemplateTypeId()];

        $templateAttributes  = $this->attributeOptions->toArray();
        $attributeName       = $templateAttributes[$this->getAttributeId()];

        return __(
            'Edit "%1" Template for "%2" Store View and "%3" Attribute',
            $templateName,
            $storeViewName,
            $attributeName
        );
    }

    /**
     *
     * @return int
     */
    protected function getTemplateStoreId()
    {
        return $this->getCategoryFilterTemplate()->getStoreId();
    }

    /**
     *
     * @return int
     */
    protected function getTemplateTypeId()
    {
        return $this->getCategoryFilterTemplate()->getTypeId();
    }

    /**
     *
     * @return int
     */
    protected function getAttributeId()
    {
        return $this->getCategoryFilterTemplate()->getAttributeId();
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template\CategoryFilter
     */
    protected function getCategoryFilterTemplate()
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

        if ($this->getCategoryFilterTemplate()->getDuplicateTemplateAssignedForAll()) {
            foreach ($options as $key => $option) {
                if ($option['value'] == \MageWorx\SeoXTemplates\Model\Template\CategoryFilter::ASSIGN_ALL_ITEMS) {
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
                    $('#template_categoryfilter_tabs_categories').parent().hide();
                    $('#template_categoryfilter_tabs_attributeset').parent().hide();
                }
            });

            $('#template_assign_type2').on('change', function() {

                if ($('#template_assign_type2:checked')) {
                    $('#template_categoryfilter_tabs_categories').parent().show();
                    $('#template_categoryfilter_tabs_attributeset').parent().hide();
                }
            });
        });
        </script>";
    }

    /**
     * @param CategoryFilterTemplate $type
     * Return comments for category template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function getComments($type)
    {
        $comment = '<small>';
        switch ($type) {
            case CategoryFilterTemplate::TYPE_CATEGORY_META_TITLE:
            case CategoryFilterTemplate::TYPE_CATEGORY_META_DESCRIPTION:
            case CategoryFilterTemplate::TYPE_CATEGORY_META_KEYWORDS:
            case CategoryFilterTemplate::TYPE_CATEGORY_DESCRIPTION:
                $comment .= '<br><p>' . $this->getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->getLnPersonalFiltersComment();
                $comment .= '<br><p>' . $this->getAdditionalComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Category Template Type'));
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
        $string = '<b>[filter_all]</b> - ' . __('inserts all chosen attributes of LN on the category page.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . " <b>" . '[category][ – parameters: {filter_all}]' . "</b>";
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
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . ' <b>[category][ in {filter_color}]</b>';
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
