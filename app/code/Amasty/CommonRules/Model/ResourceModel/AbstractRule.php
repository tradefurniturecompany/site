<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\ResourceModel;

abstract class AbstractRule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $keyAttributeTable = '';

    /**
     * Standard resource model initialization
     *
     * @param string $mainTable
     * @param string $idFieldName
     * @return void
     */
    protected function _init($mainTable, $idFieldName)
    {
        $this->_setMainTable($mainTable, $idFieldName);
        $this->setKeyAttributeTable($this->getAttributeTableNameByMainTable($mainTable));
    }

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $db = $this->getConnection();
        $tbl = $this->getTable($this->getKeyAttributeTable());
        $select = $db->select()->from($tbl, new \Zend_Db_Expr('DISTINCT code'));

        return $db->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $id rule id
     * @param mixed $attributes
     * return Amasty_Shiprestriction_Model_Mysql4_Rule
     */
    public function saveAttributes($id, $attributes)
    {
        $db = $this->getConnection();
        $tbl = $this->getTable($this->getKeyAttributeTable());

        $db->delete(
            $tbl,
            [
                'rule_id=?' => $id
            ]
        );

        $data = [];
        foreach ($attributes as $code) {
            $data[] = array(
                'rule_id' => $id,
                'code' => $code,
            );
        }
        $db->insertMultiple($tbl, $data);

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyAttributeTable()
    {
        return $this->keyAttributeTable;
    }

    /**
     * @param $keyTable
     * @return $this
     */
    public function setKeyAttributeTable($keyTable)
    {
        $this->keyAttributeTable = $keyTable;

        return $this;
    }

    /**
     * @param $mainTable
     * @return string
     */
    protected function getAttributeTableNameByMainTable($mainTable)
    {
        return str_replace("rule", "attribute", $mainTable);
    }
}
