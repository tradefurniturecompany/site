<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\ResourceModel;

class PageReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoreports_page', 'entity_id');
    }

    /**
     * @param int $referenceId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresForReference($referenceId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
                             ->from($this->getMainTable(), 'store_id')
                             ->where('reference_id = :reference_id');

        return $connection->fetchCol($select, ['reference_id' => (int)$referenceId]);
    }
}
