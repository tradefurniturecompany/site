<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Model;

use Amasty\ShippingTableRates\Api\Data\MethodInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Shipping Method Entity Model
 */
class Method extends AbstractModel implements MethodInterface
{
    const MEDIA_MODULE_DIRECTORY = 'amtable';

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploadFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    protected function _construct()
    {
        $this->_init(\Amasty\ShippingTableRates\Model\ResourceModel\Method::class);
    }

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploadFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context, $coreRegistry);
        $this->adapterFactory = $adapterFactory;
        $this->uploadFactory = $uploadFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param int[] $ids
     * @param int $status
     *
     * @return $this
     * @deprecated
     */
    public function massChangeStatus($ids, $status)
    {
        foreach ($ids as $id) {
            $model = $this->load($id);
            $model->setIsActive($status);
            $model->save();
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFreeTypes()
    {
        $result = [];
        $freeTypesString = trim($this->getData('free_types'), ',');
        if ($freeTypesString) {
            $result = explode(',', $freeTypesString);
        }

        return $result;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function saveImage($file)
    {
        $uploader = $this->uploadFactory->create(['fileId' => $file]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $mediaDirectory = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
        $result = $uploader->save(
            $mediaDirectory->getAbsolutePath(self::MEDIA_MODULE_DIRECTORY)
        );
        $img = self::MEDIA_MODULE_DIRECTORY . $result['file'];

        return $img;
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->_getData(MethodInterface::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        $this->setData(MethodInterface::IS_ACTIVE, $isActive);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(MethodInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(MethodInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getComment()
    {
        return $this->_getData(MethodInterface::COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setComment($comment)
    {
        $this->setData(MethodInterface::COMMENT, $comment);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStores()
    {
        return $this->_getData(MethodInterface::STORES);
    }

    /**
     * @inheritdoc
     */
    public function setStores($stores)
    {
        $this->setData(MethodInterface::STORES, $stores);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustGroups()
    {
        return $this->_getData(MethodInterface::CUST_GROUPS);
    }

    /**
     * @inheritdoc
     */
    public function setCustGroups($custGroups)
    {
        $this->setData(MethodInterface::CUST_GROUPS, $custGroups);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelectRate()
    {
        return $this->_getData(MethodInterface::SELECT_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setSelectRate($selectRate)
    {
        $this->setData(MethodInterface::SELECT_RATE, $selectRate);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMinRate()
    {
        return $this->_getData(MethodInterface::MIN_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setMinRate($minRate)
    {
        $this->setData(MethodInterface::MIN_RATE, $minRate);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxRate()
    {
        return $this->_getData(MethodInterface::MAX_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setMaxRate($maxRate)
    {
        $this->setData(MethodInterface::MAX_RATE, $maxRate);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFreeTypes($freeTypes)
    {
        $this->setData(MethodInterface::FREE_TYPES, $freeTypes);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCommentImg()
    {
        return $this->_getData(MethodInterface::COMMENT_IMG);
    }

    /**
     * @inheritdoc
     */
    public function setCommentImg($commentImg)
    {
        $this->setData(MethodInterface::COMMENT_IMG, $commentImg);

        return $this;
    }
}
