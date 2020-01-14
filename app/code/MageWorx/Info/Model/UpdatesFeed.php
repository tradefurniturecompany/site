<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Model;

class UpdatesFeed extends \Magento\AdminNotification\Model\Feed
{
    /**
     * @var string
     */
    const CACHE_IDENTIFIER = 'mageworx_updates_notifications_lastcheck';

    /**
     * Feed url
     *
     * @var string
     */
    protected $_feedUrl =  \MageWorx\Info\Helper\Data::MAGEWORX_SITE . '/infoprovider/index/updates';

    /**
     * Retrieve feed Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load(self::CACHE_IDENTIFIER);
    }

    /**
     * Set feed last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), self::CACHE_IDENTIFIER);

        return $this;
    }
}
