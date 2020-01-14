<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\Template;

/**
 * @api
 */
interface ManagerInterface
{
    /**
     * @return array
     */
    public function getAvailableIds();
    /**
     * @return array
     */
    public function getColumnsValues();
}
