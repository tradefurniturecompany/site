<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Model;

/**
 * Template Filter Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Filter extends \Magento\Cms\Model\Template\Filter
{
    /**
     * Replace widget codes to hash.
     *
     * @param string $value
     * @param array $replacedPairs
     * @return string
     */
    public function replace($value, &$replacedPairs)
    {
        // "depend" and "if" operands should be first
        foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN,
            self::CONSTRUCTION_IF_PATTERN,
            ) as $pattern) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $replacedValue = $this->_getRandomValue();
                    $replacedPairs[$replacedValue] = $construction[0];
                    $value = $this->strReplaceOnce($construction[0], $replacedValue, $value);
                }
            }
        }

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $replacedValue = $this->_getRandomValue();
                $replacedPairs[$replacedValue] = $construction[0];
                $value = $this->strReplaceOnce($construction[0], $replacedValue, $value);
            }
        }

        return $value;
    }

    /**
     *
     * @return string
     */
    protected function _getRandomValue()
    {
        return substr(md5(rand()), 0, 9);
    }

    /**
     * Recursive applies the callback to the elements of the given array
     *
     * @param string $func
     * @param array $array
     * @return array
     */
    protected function arrayMapRecursive($func, $array)
    {
        if (!is_array($array)) {
            $array = array();
        }

        foreach ($array as $key => $val) {
            if (is_array($array[$key])) {
                $array[$key] = $this->arrayMapRecursive($func, $array[$key]);
            } else {
                $array[$key] = call_user_func($func, $val);
            }
        }
        return $array;
    }

    /**
     * Replace once occurrence of the search string with the replacement string
     *
     * @param string $search
     * @param string $replace
     * @param string $text
     * @return string
     */
    protected function strReplaceOnce($search, $replace, $text)
    {
        $pos = strpos($text, $search);
        return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }
}
