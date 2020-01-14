<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Magento\Framework\Model\AbstractModel;

class Link extends AbstractModel implements LinkInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenu\Model\ResourceModel\Menu\Link::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(LinkInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(LinkInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLink()
    {
        return $this->_getData(LinkInterface::LINK);
    }

    /**
     * @inheritdoc
     */
    public function setLink($link)
    {
        $this->setData(LinkInterface::LINK, $link);

        return $this;
    }
}
