<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model;

class DataProvider
{
    /**
     * @var \MageWorx\SeoReports\Model\ReportDataConverter
     */
    protected $converter;

    /**
     * AbstractDataProvider constructor.
     *
     * @param \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter
     */
    public function __construct(
        \MageWorx\SeoReports\Model\ReportDataConverter $dataConverter
    ) {
        $this->converter = $dataConverter;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param \MageWorx\SeoReports\Model\ConfigInterface $reportConfig
     * @return array
     */
    public function getPreparedDataFullFormat($item, $reportConfig)
    {
        $data = [];

        foreach ($reportConfig->getConfig() as $property => $propertyConfig) {

            if (is_array($propertyConfig) && $propertyConfig) {

                $itemProperty = !empty($propertyConfig['item_property']) ? $propertyConfig['item_property'] : $property;
                $addToDb      = !array_key_exists(
                    'write_to_db',
                    $propertyConfig
                ) ? true : (bool)$propertyConfig['write_to_db'];

                foreach ($propertyConfig as $columnField => $problems) {

                    if ($addToDb) {
                        $data[$property] = $this->getItemData($item, $property, $itemProperty);
                    }

                    if (!is_array($problems) || !$problems) {
                        continue;
                    }

                    foreach ($problems as $problem => $problemConfig) {
                        if ($problem == 'length') {
                            $field        = $problemConfig['field'];
                            $data[$field] = $this->converter->getTextLength(
                                $this->getItemData($item, $property, $itemProperty),
                                $field
                            );

                        } elseif ($problem == 'duplicate') {
                            $field        = $problemConfig['param_field'];
                            $data[$field] = $this->converter->prepareText(
                                $this->getItemData($item, $property, $itemProperty),
                                $field
                            );
                        }
                    }
                }
            } else {
                $data[$property] = $this->getItemData($item, $property);
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param $key
     * @return string
     */
    protected function getItemData($item, $property, $itemProperty = null)
    {
        if ('reference_id' == $property) {
            return $item->getData($itemProperty);
        }

        if ($itemProperty === null) {
            $itemProperty = $property;
        }

        return $item->isDeleted() ? '' : $item->getData($itemProperty);
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param \MageWorx\SeoReports\Model\ConfigInterface $reportConfig
     * @return array
     */
    public function getPreparedData($item, $reportConfig)
    {
        $data = [];

        foreach ($reportConfig->getConfig() as $property => $propertyConfig) {

            if (is_array($propertyConfig) && $propertyConfig) {

                $itemProperty = !empty($propertyConfig['item_property']) ? $propertyConfig['item_property'] : $property;
                $addToDb      = !array_key_exists(
                    'write_to_db',
                    $propertyConfig
                ) ? true : (bool)$propertyConfig['write_to_db'];

                if (!$item->hasData($itemProperty)) {
                    continue;
                }

                foreach ($propertyConfig as $columnField => $problems) {

                    if ($addToDb) {
                        $data[$property] = $item->getData($itemProperty);
                    }

                    if (!is_array($problems) || !$problems) {
                        continue;
                    }

                    foreach ($problems as $problem => $problemConfig) {
                        if ($problem == 'length') {
                            $field        = $problemConfig['field'];
                            $data[$field] = $this->converter->getTextLength($item->getData($itemProperty), $field);

                        } elseif ($problem == 'duplicate') {
                            $field           = $problemConfig['param_field'];
                            $data[$property] = $item->getData($itemProperty);
                            $data[$field]    = $this->converter->prepareText($item->getData($itemProperty), $field);
                        }
                    }
                }
            }

            if (empty($data['reference_id'])) {
                $data['reference_id'] = $item->getId();
            }
        }

        return $data;
    }
}