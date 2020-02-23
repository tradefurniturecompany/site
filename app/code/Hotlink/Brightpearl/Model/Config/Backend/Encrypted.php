<?php
namespace Hotlink\Brightpearl\Model\Config\Backend;

/**
 *
 *  copy of Magento\Config\Model\Config\Backend
 *  without "implements \Magento\Framework\App\Config\Data\ProcessorInterface"
 *  to workaround 2.1.8 -> 2.2.2 breaking change (bug)
 *
 */
class Encrypted extends \Magento\Framework\App\Config\Value
{

    /**

       Problem:

          Magento 2.1.8
               \Magento\Config\Model\Config\Backend\Encrypted works fine

          Magento 2.2.2
               \Magento\Config\Model\Config\Backend\Encrypted:_afterLoad no longer called

          Thus data is never decrypted, and (when saved) ends up being enrypted twice (unusable)

    */

    protected $_encryptor;

    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, ['_encryptor']);
    }

    function __wakeup()
    {
        parent::__wakeup();
        $this->_encryptor = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Encryption\EncryptorInterface::class
        );
    }

    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = $this->_encryptor->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        // don't save value, if an obscured value was received. This indicates that data was not changed.
        if (!preg_match('/^\*+$/', $value) && !empty($value)) {
            $this->_dataSaveAllowed = true;
            $encrypted = $this->_encryptor->encrypt($value);
            $this->setValue($encrypted);
        } elseif (empty($value)) {
            $this->_dataSaveAllowed = true;
        }
    }

    function processValue($value)
    {
        return $this->_encryptor->decrypt($value);
    }

    function afterLoad()
    {
        return parent::afterLoad();
    }

}
