<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Mageplaza\Osc\Helper\Data as OscHelper;

/**
 * Class Geoip
 * @package Mageplaza\Osc\Controller\Adminhtml\System\Config
 */
class Geoip extends Action
{
    /**
     * @type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @type \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DirectoryList $directoryList
     * @param OscHelper $oscHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DirectoryList $directoryList,
        OscHelper $oscHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_directoryList    = $directoryList;
        $this->_oscHelper        = $oscHelper;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $status = false;
        try {
            $path = $this->_directoryList->getPath('var') . '/Mageplaza/Osc/GeoIp';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $folder   = scandir($path, true);
            $pathFile = $path . '/' . $folder[0] . '/GeoLite2-City.mmdb';
            if (file_exists($pathFile)) {
                foreach (scandir($path . '/' . $folder[0], true) as $filename) {
                    if ($filename == '..' || $filename == '.') {
                        continue;
                    }
                    @unlink($path . '/' . $folder[0] . '/' . $filename);
                }
                @rmdir($path . '/' . $folder[0]);
            }

            file_put_contents($path . '/GeoLite2-City.tar.gz', fopen($this->_oscHelper->getDownloadPath(), 'r'));
            $phar = new \PharData($path . '/GeoLite2-City.tar.gz');
            $phar->extractTo($path);
            $status  = true;
            $message = __("Download library success!");
        } catch (\Exception $e) {
            $message = __("Can't download file. Please try again! %1", $e->getMessage());
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => $status, 'message' => $message]);
    }
}
