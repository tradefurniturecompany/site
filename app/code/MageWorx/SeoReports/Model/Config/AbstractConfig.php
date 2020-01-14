<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Config;

abstract class AbstractConfig implements \MageWorx\SeoReports\Model\ConfigInterface
{
    /**
     * @return array
     */
    abstract public function getConfig();

    /**
     * @return array
     */
    public function getFieldList()
    {
        $fields = [];

        foreach ($this->getConfig() as $property => $propertyConfig) {
            if (!empty($propertyConfig['item_property'])) {
                $fields[] = $propertyConfig['item_property'];
            } else {
                $fields[] = $property;
            }
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getConfigProblemSections()
    {
        $data = [];

        foreach ($this->getConfig() as $property => $propertyConfig) {
            if (is_array($propertyConfig) && !empty($propertyConfig)) {
                foreach ($propertyConfig as $columnField => $problems) {

                    if ($columnField == 'item_property') {
                        continue;
                    }

                    $data[$columnField] = $problems;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getDuplicateColumnData()
    {
        $result = [];

        foreach ($this->getConfig() as $field => $fieldSections) {
            if (is_array($fieldSections) && !empty($fieldSections)) {

                foreach ($fieldSections as $name => $fieldProblems) {

                    if ($name == 'item_property') {
                        continue;
                    }

                    if (!empty($fieldProblems['duplicate']['field'])) {
                        $result[] = [
                            'column'           => $field,
                            'param_column'     => $fieldProblems['duplicate']['param_field'],
                            'duplicate_column' => $fieldProblems['duplicate']['field']
                        ];
                        continue;
                    }
                }
            }
        }

        return $result;
    }
}
