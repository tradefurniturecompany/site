<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Observer\Category;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Model\AbstractModel;
use Amasty\MegaMenu\Model\Menu\ItemFactory;
use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Magento\Framework\App\RequestInterface;

class ContentSave implements ObserverInterface
{
    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ItemFactory $itemFactory,
        ItemRepositoryInterface $itemRepository,
        RequestInterface $request
    ) {
        $this->itemFactory = $itemFactory;
        $this->itemRepository = $itemRepository;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $entity = $observer->getEvent()->getEntity();
        if ($entity instanceof AbstractModel) {
            $storeId = $this->request->getParam('store_id', $entity->getStoreId());
            $itemContent = $this->itemRepository->getByEntityId($entity->getId(), $storeId, 'category');
            if (!$itemContent) {
                $itemContent = $this->itemFactory->create([
                    'data' => [
                        'store_id'  => $storeId,
                        'type'      => 'category',
                        'entity_id' => $entity->getId()
                    ]
                ]);
            }

            foreach (ItemInterface::FIELDS_BY_STORE_CATEGORY as $fieldSet) {
                foreach ($fieldSet as $field) {
                    $itemContent->setData($field, $entity->getData($field));
                }
            }
            $itemContent->setName($entity->getName());
            $itemContent->setStatus($entity->getIsActive() && $entity->getIncludeInMenu());

            $this->itemRepository->save($itemContent);
        }
    }
}
