<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Tab with Labels store scoped
 */
class Labels extends Generic implements TabInterface
{
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Label
     */
    private $labelFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializerBase;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\ShippingTableRates\Model\LabelFactory $labelFactory,
        \Amasty\Base\Model\Serializer $serializerBase,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->labelFactory = $labelFactory;
        $this->serializerBase = $serializerBase;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Labels');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Labels');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Amasty\ShippingTableRates\Model\Method $model */
        $model = $this->_coreRegistry->registry('current_amasty_table_method');
        /** @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection */
        $collection = $model->getCollection();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        /** @var \Amasty\ShippingTableRates\Model\Label $formModel */
        if ($model->getId()) {
            $collection->joinLabels($model->getId());
        }
        $form->setHtmlIdPrefix('amstrates_');
        $storesData = $this->prepareStoresData($collection);
        $this->createLabelsFieldset($form, $storesData);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection
     *
     * @return array
     */
    private function prepareStoresData(\Amasty\ShippingTableRates\Model\ResourceModel\Method\Collection $collection)
    {
        $storesData = [];
        foreach ($collection->getData() as $storeData) {
            if (isset($storeData['store_id'])) {
                $storesData[$storeData['store_id']]['label'] = isset($storeData['label'])
                    ? $storeData['label']
                    : '';
                $storesData[$storeData['store_id']]['comment'] = isset($storeData['comment'])
                    ? $storeData['comment']
                    : '';
            }
        }

        return $storesData;
    }

    /**
     * Create store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param $storesData
     *
     * @return Fieldset
     */
    private function createLabelsFieldset($form, $storesData)
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldSet = $form->addFieldset(
                'store_labels_fieldset',
                [
                    'legend' => __('Store View Specific Labels'),
                    'table_class' => 'form-list stores-tree',
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset::class
            );
            $fieldSet->setRenderer($renderer);
            $this->createStoreSpecificLabels($fieldSet, $storesData);
        } else {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
            $fieldSet = $form->addFieldset(
                'store_labels_fieldset',
                [
                    'legend' => __('Default Label for All Store Views'),
                    'table_class' => 'form-list stores-tree',
                ]
            );
            $this->addFields($fieldSet, $storeId, $storesData);
        }

        return $fieldSet;
    }

    /**
     * @param Fieldset $fieldSet
     * @param array $storesData
     */
    private function createStoreSpecificLabels(Fieldset $fieldSet, $storesData)
    {
        /** @var \Magento\Store\Api\Data\WebsiteInterface[] $webSites */
        $webSites = $this->_storeManager->getWebsites();

        /** @var \Magento\Store\Api\Data\WebsiteInterface $webSite */
        foreach ($webSites as $webSite) {
            $fieldSet->addField(
                "w_{$webSite->getId()}_label",
                'note',
                [
                    'label' => $webSite->getName(),
                    'fieldset_html_class' => 'website',
                ]
            );

            /** @var \Magento\Store\Model\Group $group */
            foreach ($webSite->getGroups() as $group) {
                $stores = $group->getStores();
                $countStores = count($stores);
                if ($countStores == 0) {
                    continue;
                }
                $fieldSet->addField(
                    "sg_{$group->getId()}_label",
                    'note',
                    [
                        'label' => $group->getName(),
                        'fieldset_html_class' => 'store-group',
                    ]
                );

                /** @var \Magento\Store\Model\Store $store */
                foreach ($stores as $store) {
                    $storeId = $store->getId();
                    $fieldSet->addField(
                        "s_{$storeId}_label",
                        'note',
                        [
                            'label' => $store->getName() . ':',
                        ]
                    );
                    $this->addFields($fieldSet, $storeId, $storesData);
                }
            }
        }
    }

    /**
     * @param Fieldset $fieldSet
     * @param int $storeId
     * @param array $storesData
     */
    private function addFields(Fieldset $fieldSet, $storeId, $storesData)
    {
        $fieldSet->addField(
            "label-{$storeId}",
            'text',
            [
                'name' => 'label_[' . $storeId . ']',
                'required' => false,
                'label' => __('Method Label'),
                'value' => isset($storesData[$storeId])
                    ? $storesData[$storeId]['label']
                    : '',
                'fieldset_html_class' => 'store',
            ]
        );
        $fieldSet->addField(
            "comment-{$storeId}",
            'textarea',
            [
                'name' => 'comment_[' . $storeId . ']',
                'required' => false,
                'label' => __('Comment'),
                'style' => 'height:15px;',
                'value' => isset($storesData[$storeId])
                    ? $storesData[$storeId]['comment']
                    : '',
                'fieldset_html_class' => 'store',
            ]
        );
    }
}
