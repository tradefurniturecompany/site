<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template;

use MageWorx\SeoXTemplates\Model\Template\Product as TemplateProductModel;

/**
 * Product template mysql resource
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var string
     */
    protected $productRelationTable      = 'mageworx_seoxtemplates_template_relation_product';

    /**
     *
     * @var string
     */
    protected $attributesetRelationTable = 'mageworx_seoxtemplates_template_relation_attributeset';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates_template_product', 'template_id');
    }

    /**
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->loadItems();
        return parent::_afterLoad($object);
    }

    /**
     * Process template data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['template_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable($this->productRelationTable), $condition);
        $this->getConnection()->delete($this->getTable($this->attributesetRelationTable), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param TemplateProductModel $template
     * @return $this
     */
    public function saveProductRelation(TemplateProductModel $template)
    {
        $id = $template->getId();
        $products = $template->getProductsData();
        $this->clearAllRelations($template);

        if (!empty($products)) {
            $data = [];
            foreach ($products as $productId) {
                $data[] = [
                    'template_id' => (int)$id,
                    'product_id'  => (int)$productId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable($this->productRelationTable), $data);
        }

        return $this;
    }

    /**
     * Clear all template relations
     *
     * @param TemplateCategoryModel $template
     * @return \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category
     */
    public function clearProductRelation(TemplateProductModel $template)
    {
        $id = $template->getId();
        $condition = ['template_id=?' => $id];
        $this->getConnection()->delete($this->getTable($this->productRelationTable), $condition);
        $template->setIsChangedProductList(true);

        return $this;
    }

    public function saveAttributesetRelation(TemplateProductModel $template)
    {
        $id = $template->getId();
        $attributesetId = $template->getAttributesetValue();

        $this->clearAllRelations($template);

        if ($attributesetId) {
            $data = [
                'template_id'     => (int)$id,
                'attributeset_id' => (int)$attributesetId,
            ];

            $this->getConnection()->insert($this->getTable($this->attributesetRelationTable), $data);
        }
        $template->setIsChangedAttributesetList(true);

        return $this;
    }

    public function clearAttributesetRelation(TemplateProductModel $template)
    {
        $id = $template->getId();
        $condition = ['template_id=?' => $id];
        $this->getConnection()->delete($this->getTable($this->attributesetRelationTable), $condition);
        $template->setIsChangedAttributesetList(true);

        return $this;
    }

    public function clearAllRelations(TemplateProductModel $template)
    {
        $this->clearProductRelation($template);
        $this->clearAttributesetRelation($template);

        return $this;
    }

    /**
     * Retrieve individual item ids by template(s)
     *
     * @param int|array $templateId
     * @return array
     */
    public function getIndividualItemIds($templateId)
    {
        if (!is_array($templateId)) {
            $templateId = [$templateId];
        }

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable($this->productRelationTable), new \Zend_Db_Expr("DISTINCT `product_id`"))
            ->where('template_id IN (?)', $templateId);

        $result = [];
        $data   = $this->getConnection()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }

    /**
     * Retrieve group item ids by template(s)
     *
     * @param int $templateId
     * @return array
     */
    public function getGroupItemId($templateId)
    {
        if (!is_array($templateId)) {
            $templateId = [$templateId];
        }

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable($this->attributesetRelationTable), new \Zend_Db_Expr("DISTINCT `attributeset_id`"))
            ->where('template_id IN (?)', $templateId);

        $result = [];
        $data   = $this->getConnection()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }

        return $result;
    }
}
