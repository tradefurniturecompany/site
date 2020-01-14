<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoBase\Model\Source\XDefault;

class GlobalScope extends \MageWorx\SeoBase\Model\Source\XDefault\WebsiteScope
{
    protected $options;

    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_unshift($options, ['label'=> __('--Please Select--'), 'value' => '0']);
        return $options;
    }

    protected function cmp($a, $b)
    {
        $orderBy = ['website_id' => 'asc', 'value' => 'asc'];
        $result = 0;
        foreach ($orderBy as $key => $value) {
            if ($a[$key] == $b[$key]) {
                continue;
            }
            $result = ($a[$key] < $b[$key]) ? -1 : 1;
            if ($value == 'desc') {
                $result = -$result;
                break;
            }
        }
        return $result;
    }
}
