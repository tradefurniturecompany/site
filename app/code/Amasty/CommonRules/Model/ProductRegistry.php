<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model;

abstract class ProductRegistry
{
    /**
     * @var array
     */
    protected $products = [];

    /**
     * @param $name
     * @return $this
     */
    public function addProduct($name)
    {
        if (!in_array($name, $this->products)) {
            $this->products [] = $name;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearProducts()
    {
        $this->products = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $products
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->products = $products;

        return $this;
    }
}