<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template;

use MageWorx\SeoXTemplates\Model\Template\LandingPage as TemplateLandingPageModel;

/**
 * LandingPage template mysql resource
 */
class LandingPage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var string
     */
    protected $landingPageRelationTable = 'mageworx_seoxtemplates_template_relation_landingpage';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates_template_landingpage', 'template_id');
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

        $this->getConnection()->delete($this->getTable($this->landingPageRelationTable), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param TemplateLandingPageModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveLandingPageRelation(TemplateLandingPageModel $template)
    {
        $id           = $template->getId();
        $landingPages = $template->getLandingPagesData();
        $this->clearAllRelations($template);
        if (!empty($landingPages)) {
            $data = [];
            foreach ($landingPages as $landingPageId) {
                $data[] = [
                    'template_id'    => (int)$id,
                    'landingpage_id' => (int)$landingPageId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable($this->landingPageRelationTable), $data);
        }

        return $this;
    }

    /**
     * @param TemplateLandingPageModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearLandingPageRelation(TemplateLandingPageModel $template)
    {
        $id        = $template->getId();
        $condition = ['template_id=?' => $id];
        $this->getConnection()->delete($this->getTable($this->landingPageRelationTable), $condition);
        $template->setIsChangedLandingPageList(true);

        return $this;
    }

    /**
     * @param TemplateLandingPageModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearAllRelations(TemplateLandingPageModel $template)
    {
        $this->clearLandingPageRelation($template);

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
                       ->from(
                           $this->getTable($this->landingPageRelationTable),
                           new \Zend_Db_Expr("DISTINCT `landingpage_id`")
                       )
                       ->where('template_id IN (?)', $templateId);

        $result = [];
        $data   = $this->getConnection()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }

        return $result;
    }
}
