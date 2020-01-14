<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template;

use MageWorx\SeoXTemplates\Model\Template\Category as TemplateCategoryModel;

/**
 * Category template mysql resource
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var string
     */
    protected $categoryRelationTable      = 'mageworx_seoxtemplates_template_relation_category';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates_template_category', 'template_id');
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

        $this->getConnection()->delete($this->getTable($this->categoryRelationTable), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     *
     * @param TemplateCategoryModel $template
     * @return $this
     */
    public function saveCategoryRelation(TemplateCategoryModel $template)
    {
        $id = $template->getId();
        $categories = $template->getCategoriesData();
        $this->clearAllRelations($template);

        if (!empty($categories)) {
            $data = [];
            foreach ($categories as $categoryId) {
                $data[] = [
                    'template_id' => (int)$id,
                    'category_id'  => (int)$categoryId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable($this->categoryRelationTable), $data);
        }

        return $this;
    }

    /**
     *
     * @param TemplateCategoryModel $template
     * @return \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category
     */
    public function clearCategoryRelation(TemplateCategoryModel $template)
    {
        $id = $template->getId();
        $condition = ['template_id=?' => $id];
        $this->getConnection()->delete($this->getTable($this->categoryRelationTable), $condition);
        $template->setIsChangedCategoryList(true);

        return $this;
    }

    /**
     * Clear all template relations
     *
     * @param TemplateCategoryModel $template
     * @return \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category
     */
    public function clearAllRelations(TemplateCategoryModel $template)
    {
        $this->clearCategoryRelation($template);

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
            ->from($this->getTable($this->categoryRelationTable), new \Zend_Db_Expr("DISTINCT `category_id`"))
            ->where('template_id IN (?)', $templateId);

        $result = [];
        $data   = $this->getConnection()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }
}
