<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

/**
 * {@inheritdoc}
 */
interface GeneratorInterface
{
    /**
     * @param $storeId
     * @param $writer
     * @return mixed
     */
    public function generate($storeId, $writer);

    /**
     * @return int
     */
    public function getCounter();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getName();
}