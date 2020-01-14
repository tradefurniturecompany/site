<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Block\Adminhtml\Crosslink\Edit\Tab;

use MageWorx\SeoCrossLinks\Model\Crosslink;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\System\Store;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoCrossLinks\Model\Crosslink\Source\IsActive as LinkIsActive;
use MageWorx\SeoCrossLinks\Model\Crosslink\Source\LinkTo as LinkToOptions;
use MageWorx\SeoCrossLinks\Model\Crosslink\Source\Target as LinkTargetOptions;
use MageWorx\SeoCrossLinks\Model\Crosslink\Source\Crosslink\CrosslinkTypeKey as CrosslinkTypeKeyOptions;
use MageWorx\SeoAll\Helper\LandingPage;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class Main extends \Magento\Widget\Block\Adminhtml\Widget\Options  implements TabInterface
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
     * @var LinkIsActiveOptions
     */
    protected $linkIsActiveOptions;

    /**
     * @var LinkToOptions
     */
    protected $linkToOptions;

    /**
     * @var LinkTargetOptions
     */
    protected $linkTargetOptions;

    /**
     * @var CrosslinkTypeKeyOptions
     */
    protected $crosslinkTypeKeyOptions;

    /**
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * Main constructor.
     *
     * @param LandingPage $landingPage
     * @param Store $systemStore
     * @param BooleanOptions $booleanOptions
     * @param LinkIsActive $linkIsActive
     * @param LinkToOptions $linkToOptions
     * @param LinkTargetOptions $linkTargetOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Option\ArrayPool $sourceModelPool
     * @param \Magento\Widget\Model\Widget $widget
     * @param CrosslinkTypeKeyOptions $crosslinkTypeKeyOptions
     * @param array $data
     */
    public function __construct(
        LandingPage $landingPage,
        Store $systemStore,
        BooleanOptions $booleanOptions,
        LinkIsActive $linkIsActive,
        LinkToOptions $linkToOptions,
        LinkTargetOptions $linkTargetOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Option\ArrayPool $sourceModelPool,
        \Magento\Widget\Model\Widget $widget,
        CrosslinkTypeKeyOptions $crosslinkTypeKeyOptions,
        ProductResource $productResource,
        array $data = []
    ) {
        $this->landingPage             = $landingPage;
        $this->systemStore             = $systemStore;
        $this->booleanOptions          = $booleanOptions;
        $this->linkIsActiveOptions     = $linkIsActive;
        $this->linkToOptions           = $linkToOptions;
        $this->linkTargetOption        = $linkTargetOptions;
        $this->crosslinkTypeKeyOptions = $crosslinkTypeKeyOptions;
        $this->productResource         = $productResource;
        parent::__construct($context, $registry, $formFactory, $sourceModelPool, $widget, $data);
    }

    public function addFields()
    {
        return $this;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoCrossLinks\Model\Crosslink $crosslink */
        $crosslink = $this->_coreRegistry->registry('mageworx_seocrosslinks_crosslink');

        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('crosslink');

        /** @var  \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Crosslink Info'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($crosslink->getId()) {
            $fieldset->addField(
                'crosslink_id',
                'hidden',
                ['name' => 'crosslink_id']
            );
        }

        $fieldset->addField(
            'keyword',
            'textarea',
            [
                'name'      => 'keyword',
                'label'     => __('Keyword'),
                'title'     => __('Keyword'),
                'required'  => true,
                'note'      => $this->getKeywordFieldNote(),
            ]
        );

        $fieldset->addField(
            'link_title',
            'text',
            [
                'name'      => 'link_title',
                'label'     => __('Link Alt/Title'),
                'title'     => __('Link Alt/Title'),
            ]
        );

        $fieldset->addField(
            'link_target',
            'select',
            [
                'label'     => __('Link Target'),
                'title'     => __('Link Target'),
                'name'      => 'link_target',
                'required'  => true,
                'options'   => $this->linkTargetOption->toArray()
            ]
        );

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'      => 'stores[]',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $crosslink->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name'     => 'stores[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->systemStore->getStoreValuesForForm(false, true),
                    'note'     =>__('NOTE: Cross Link will be build in the chosen store views.'),
                ]
            );
        }

        $reference = $fieldset->addField(
            'reference',
            'select',
            [
                'label'     => __('Reference'),
                'name'      => 'reference',
                'values'    => $this->linkToOptions->toOptionArray(),
                'note'      => __("NOTE: Cross link will not be shown on the page if this page is specified as target page for this keyword.")
            ]
        );

        $url = $fieldset->addField(
            CrosslinkTypeKeyOptions::URL_REQUEST_IDENTIFIER,
            'text',
            [
                'label'    => __('Custom URL'),
                'name'     => CrosslinkTypeKeyOptions::URL_REQUEST_IDENTIFIER,
                'index'    => CrosslinkTypeKeyOptions::URL_REQUEST_IDENTIFIER,
                'class'    => 'required-entry',
                'required' => true,
                'note'     => $this->getStaticUrlFieldNote()
            ]
        );

        $category = $this->addCategoryChooserField(
            $fieldset,
            CrosslinkTypeKeyOptions::CATEGORY_REQUEST_IDENTIFIER,
            $crosslink->getRefCategoryId()
        );

        $product  = $this->addProductChooserField(
            $fieldset,
            CrosslinkTypeKeyOptions::PRODUCT_REQUEST_IDENTIFIER,
            $crosslink->getRefProductSku()
        );
        if ($this->landingPage->isLandingPageEnabled()) {
            $landingpage = $this->addLandingpageChooserField(
                $fieldset,
                CrosslinkTypeKeyOptions::LANDINGPAGE_REQUEST_IDENTIFIER,
                $crosslink->getRefLandingpageId()
            );
        }

        $fieldset->addField(
            'replacement_count',
            'text',
            [
                'label'    => __('Max Replacement Count per Page'),
                'name'     => 'replacement_count',
                'index'    => 'replacement_count',
                'class'    => 'required-entry not-negative-amount integer validate-number-range number-range-0-100',
                'note'     => __('Max # of this keyword per page. 100 is the max value.'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'label'    => __('Priority'),
                'name'     => 'priority',
                'index'    => 'priority',
                'class'    => 'required-entry not-negative-amount integer validate-number-range number-range-0-100',
                'note'     => __('100 is the highest priority.'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'nofollow_rel',
            'select',
            [
                'label'     => __('Nofollow'),
                'title'     => __('Nofollow'),
                'name'      => 'nofollow_rel',
                'required'  => false,
                'options'   => $this->booleanOptions->toArray()
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'     => __('Is Active'),
                'title'     => __('Is Active'),
                'name'      => 'is_active',
                'required'  => true,
                'options'   => $this->linkIsActiveOptions->toArray()
            ]
        );

        $dependency = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        )->addFieldMap(
            $reference->getHtmlId(),
            $reference->getName()
        )->addFieldMap(
            $url->getHtmlId(),
            $url->getName()
        )->addFieldMap(
            $product->getHtmlId(),
            $product->getName()
        )->addFieldMap(
            $category->getHtmlId(),
            $category->getName()
        )->addFieldDependence(
            $url->getName(),
            $reference->getName(),
            Crosslink::REFERENCE_TO_STATIC_URL
        )->addFieldDependence(
            $product->getName(),
            $reference->getName(),
            Crosslink::REFERENCE_TO_PRODUCT_BY_SKU
        )->addFieldDependence(
            $category->getName(),
            $reference->getName(),
            Crosslink::REFERENCE_TO_CATEGORY_BY_ID
        );
        if ($this->landingPage->isLandingPageEnabled()) {
            $dependency->addFieldMap(
                $landingpage->getHtmlId(),
                $landingpage->getName()
            )->addFieldDependence(
                $landingpage->getName(),
                $reference->getName(),
                Crosslink::REFERENCE_TO_LANDINGPAGE_BY_ID
            );
        }

        $this->setChild('form_after', $dependency);

        $this->addJsDependency($reference);

        $crosslinkData = $this->_session->getData('mageworx_seocrosslinks_crosslink_data', true);
        if ($crosslinkData) {
            $crosslink->addData($crosslinkData);
        } else {
            if (!$crosslink->getId()) {
                $crosslink->addData($crosslink->getDefaultValues());
            }
        }

        /** For avoid display their ids as field value - we show the names instead. */
        $crosslink->unsetData(CrosslinkTypeKeyOptions::CATEGORY_REQUEST_IDENTIFIER);
        $crosslink->unsetData(CrosslinkTypeKeyOptions::PRODUCT_REQUEST_IDENTIFIER);

        $form->addValues($crosslink->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param $name
     * @param null $value
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addCategoryChooserField($fieldset, $name, $value = null)
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'      => $name,
                'label'     =>  __('Select Category...'),
                'required'  => true,
                'index'     => $name,
                'class'     => 'widget-option',
                'value'     => 'category/' . $value,
            ]
        );

        /** @var \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser $helperBlock */
        $helperBlock = $this->getLayout()->createBlock(
            '\Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser',
            '',
            ['data' => []]
        );

        $helperBlock->setConfig(
            []
        )->setFieldsetId(
            $fieldset->getId()
        )->prepareElementHtml(
            $field
        );

        return $field;
    }

    /**
     * @param $fieldset
     * @param $name
     * @param null $value
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addProductChooserField($fieldset, $name, $value = null)
    {
        if ($value) {
            $value = $this->productResource->getIdBySku($value);
        }

        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'      => $name,
                'label'     =>  __('Select Product...'),
                'required'  => true,
                'class'     => 'widget-option',
                'index'    => 'ref_product_sku',
                'value'     => 'product/' . $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser',
            '',
            ['data' => []]
        );

        if ($helperBlock instanceof \Magento\Framework\DataObject) {
            $helperBlock->setConfig(
                []
            )->setFieldsetId(
                $fieldset->getId()
            )->prepareElementHtml(
                $field
            );
        }

        return $field;
    }

    /**
     * @param $fieldset
     * @param $name
     * @param null $value
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addLandingpageChooserField($fieldset, $name, $value = null)
    {
        $field = $fieldset->addField(
            $name,
            'label',
            [
                'name'      => $name,
                'label'     =>  __('Select Landing Page...'),
                'required'  => true,
                'class'     => 'widget-option',
                'index'    => 'ref_landingpage_id',
                'value'     => $value,
            ]
        );

        $helperBlock = $this->getLayout()->createBlock(
            'MageWorx\SeoAll\Block\Adminhtml\LandingPage\LandingpageGrid',
            '',
            ['data' => []]
        );

        if ($helperBlock instanceof \Magento\Framework\DataObject) {
            $helperBlock->setConfig(
                []
            )->setFieldsetId(
                $fieldset->getId()
            )->prepareElementHtml(
                $field
            );
        }

        return $field;
    }

    /**
     * @param $reference
     * @return $this
     */
    protected function addJsDependency($reference)
    {
        $referenceOptions = json_encode($this->getCrosslinksTypeReferenceKeyEntityOptions());

        $reference->setAfterElementHtml("<script>

            require(['jquery'], function($) {
               
                var referenceOptions = $referenceOptions;
                               
                function actionChooser(action, entityName) {
                    
                    var inputElementClass = '.field-chooser' + entityName + ' input';
                    var fieldClass = '.field-' + entityName;
                    var chooserClass = '.field-chooser' + entityName;
                             
                    if (action == 'hide') {
                        $(inputElementClass).removeClass('required-entry');
                        $(fieldClass).hide();                    
                        $(chooserClass).hide();
                    } 
                    if (action == 'show') {                    
                        $(inputElementClass).addClass('required-entry');
                        $(fieldClass).show();                    
                        $(chooserClass).show();
                    }
                }
                    
                $('#reference').on('change', function() {
                    
                    for (key in referenceOptions) {
                        actionChooser('hide', referenceOptions[key]);
                    }                    
                    var value = $(this).val();
                    actionChooser('show', referenceOptions[value]);
                });
                                              
                $('document').ready(function () {
                   $('#reference').trigger('change');                  
                });
            }
        );
        </script>");

        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Cross Link');
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

    protected function getKeywordFieldNote()
    {
        $hrefBefore = '<a href="http://support.mageworx.com/extensions/seo_suite_pro_and_ultimate/how_to_add_keywords_for_creating_internal_links.html" target="_blank">';
        $hrefAfter  = '</a>';

        $note = '<p>' . __("NOTE: Enter one keyword (keyword phrase) per line. "
                . "A new cross link rule will be created for each entered keyword.");

        $note .= '</p><p>' . __("For multiple keywords use the Reduced Multisave Priority feature."
                . " It reduces the keyword priority for every next keyword on the list "
                . "(thus, the most important keywords appear in the first place).");

        $note .= '</p><p>' . __("Adding '+' before or after a keyword will apply the Cross Link rule to all its variations. "
                . "E.g. Entering 'iphone 5+' will apply the rule to 'iphone 5s', 'iphone 5c', etc. (but not to 'iphone 5').") . '</p>';

        $note .= '<p>' . __('For more info, follow the %1 link %2.', $hrefBefore, $hrefAfter) . '</p>';

        return $note;
    }

    protected function getStaticUrlFieldNote()
    {
        $note = '<p>';
        $note .= __("Link without 'http[s]://' as customer/account/<br>will be converted to<br>http[s]://(store_URL_here)/customer/account/");
        $note .= '</p><p>';
        $note .= __("Link with 'http[s]://' will be added as it is.");
        $note .= '</p>';
        return $note;
    }

    /**
     * @return array
     */
    protected function getCrosslinksTypeReferenceKeyEntityOptions()
    {
        $referenceKeyOptions = $this->crosslinkTypeKeyOptions->toArray();
        unset($referenceKeyOptions[Crosslink::REFERENCE_TO_STATIC_URL]);
        return $referenceKeyOptions;
    }
}
