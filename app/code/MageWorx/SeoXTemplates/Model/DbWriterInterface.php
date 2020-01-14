<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model;

/**
 * @api
 */
interface DbWriterInterface
{
    /**
     * Write to database converted string from template code
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return array|boolean
     */
    public function write($collection, $template, $customStoreId = null);
}
