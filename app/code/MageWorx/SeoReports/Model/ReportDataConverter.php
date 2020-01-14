<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;


class ReportDataConverter
{
    /**
     * @param string $str
     * @param string $field
     * @return string
     */
    public function trimText($str, $field)
    {
        if (!$str) {
            return '';
        }

        return trim(preg_replace("/\s+/uis", ' ', $str));
    }

    /**
     * @param string $str
     * @param string $field
     * @return string
     */
    public function prepareText($str, $field)
    {
        if (!$str) {
            return '';
        }

        $str = strtolower(preg_replace("/[^\w\d]+/uis", ' ', $str));

        return $this->trimText($str, $field);
    }

    /**
     * @param string $text
     * @param string $field
     * @return int
     */
    public function getTextLength($text, $field)
    {
        return mb_strlen($this->trimText($text, $field), 'UTF-8');
    }
}
