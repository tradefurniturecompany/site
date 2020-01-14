<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Plugin\Catalog\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\DataProvider as NativeDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class DataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    public function __construct(
        PoolInterface $pool
    ) {
        $this->pool = $pool;
    }

    /**
     * @param NativeDataProvider $subject
     * @param array $data
     *
     * @return array
     */
    public function afterGetData(NativeDataProvider $subject, $data)
    {
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    /**
     * @param NativeDataProvider $subject
     * @param array $data
     *
     * @return array
     */
    public function afterGetMeta(NativeDataProvider $subject, $data)
    {
        if (!isset($meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice'])) {
            foreach ($this->pool->getModifiersInstances() as $modifier) {
                $data = $modifier->modifyMeta($data);
            }
        }

        return $data;
    }
}
