<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ColorTemplate implements OptionSourceInterface
{
    const CUSTOM = 'custom';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [
            ['value' => self::CUSTOM, 'label' => __('Custom')]
        ];
        foreach ($this->getData() as $key => $config) {
            $result[] = ['value' => $key, 'label' => $config['title']];
        }

        return $result;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLabelByValue($value)
    {
        foreach ($this->toOptionArray() as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
