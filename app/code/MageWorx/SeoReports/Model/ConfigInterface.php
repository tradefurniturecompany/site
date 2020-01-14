<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

/**
 * @api
 */
interface ConfigInterface
{
    /**
     * @return []
     */
    public function getConfig();

    /**
     * @return []
     */
    public function getFieldList();

    /**
     * @return []
     */
    public function getDuplicateColumnData();

    /**
     * @return mixed
     */
    public function getConfigProblemSections();
}