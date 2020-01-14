<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

/**
 * @api
 */
interface GeneratorInterface
{
    /**
     * @param \Magento\Framework\DataObject|null $entity
     * @return mixed
     */
    public function generate($entity = null);
}
