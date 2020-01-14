<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Ui\DataProvider\Form\Link;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\App\RequestInterface;
use Amasty\MegaMenu\Model\Menu\Link;
use Amasty\MegaMenu\Model\OptionSource\CmsPage;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\MegaMenu\Model\ResourceModel\Menu\Link\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        PoolInterface $pool,
        ItemRepositoryInterface $itemRepository,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->pool = $pool;
        $this->itemRepository = $itemRepository;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $result = parent::getData();

        /** @var Link $current */
        $current = $this->coreRegistry->registry(LinkInterface::PERSIST_NAME);
        if ($current && $current->getEntityId()) {
            $data = $current->getData();
            if ($data[LinkInterface::TYPE] == UrlKey::LANDING_PAGE) {
                $data['landing_page'] = $data[LinkInterface::PAGE_ID];
                $data[LinkInterface::PAGE_ID] = CmsPage::NO;
            }
            if ($storeId = (int)$this->request->getParam('store', 0)) {
                $data['store_id'] = $storeId;
                /** @var ItemInterface $item */
                $item = $this->itemRepository->getByEntityId($current->getEntityId(), $storeId, 'custom');
                if ($item) {
                    foreach (ItemInterface::FIELDS_BY_STORE_CUSTOM as $fieldSet) {
                        foreach ($fieldSet as $field) {
                            if ($item->getData($field) !== null) {
                                $data[$field] = $item->getData($field);
                            }
                        }
                    }
                }
            }
            $result[$current->getEntityId()] = $data;
        } else {
            $data = $this->dataPersistor->get(LinkInterface::PERSIST_NAME);
            if (!empty($data)) {
                /** @var Link $pack */
                $link = $this->collection->getNewEmptyItem();
                $link->setData($data);
                $data = $link->getData();
                $result[$link->getId()] = $data;
                $this->dataPersistor->clear(LinkInterface::PERSIST_NAME);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
