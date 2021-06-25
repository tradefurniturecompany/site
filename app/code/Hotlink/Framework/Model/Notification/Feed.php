<?php
namespace Hotlink\Framework\Model\Notification;

class Feed extends \Magento\AdminNotification\Model\Feed
{

    const XML_LAST_UPDATE_PATH = 'system/adminnotification/hotlink_last_update';

    public function getFeedUrl()
    {
        if ( is_null( $this->_feedUrl ) )
            {
                $result = [];
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $config = $objectManager->get( 'Hotlink\Framework\Model\Config\Map' );
                foreach ( $config->getPlatforms() as $class )
                    {
                        $platform = $objectManager->get( $class );
                        $result[] = $platform->getIdentifier();
                    }
                $filter = urlencode( implode( '|', $result ) );
                $this->_feedUrl = "http://www.hotlink.technology/magento/notification/feed.xml?filter=$filter";
            }
        return $this->_feedUrl;
    }

    public function getFrequency()
    {
        //return 0;
        return 12 * 3600;
    }

    public function getLastUpdate()
    {
        return $this->_cacheManager->load( 'hotlink_notifications_lastcheck' );
    }

    /* workaround M2 bug */
    public function getFeedData()
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(
            [
                'timeout'   => 2,
                'useragent' => $this->productMetadata->getName()
                . '/' . $this->productMetadata->getVersion()
                . ' (' . $this->productMetadata->getEdition() . ')',
                'referer'   => $this->urlBuilder->getUrl('*/*/*')
            ]
        );
        $curl->write(\Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        if ( !isset( $data[1] ) ) return false;
        $data = trim($data[1]);
        $curl->close();
        try {
            $xml = new \SimpleXMLElement($data);
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }


    public function setLastUpdate()
    {
        $this->_cacheManager->save( time(), 'hotlink_notifications_lastcheck' );
        return $this;
    }

}
