<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Helper;

/**
 * SEO Redirects string helper
 */
class StringHelper
{
    /**
     * @param string $string
     * @param array $substringList
     * @param bool $isConsecutive
     * @return string
     */
    public function cropFirstPart($string, array $substringList, $isConsecutive = false)
    {
        $resultString  = $string;
        $substringList = array_filter($substringList);

        foreach ($substringList as $substring) {
            $pos = mb_strpos($string, $substring);

            if ($pos === 0) {
                $strlen       = mb_strlen($substring);
                $resultString = mb_substr($string, $strlen);
                $string       = $resultString;

                if (!$isConsecutive) {
                    break;
                }
            }
        }

        return $resultString;
    }
}
