<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CmsPage
 */
class CmsPage implements OptionSourceInterface
{
    const NO = 0;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    public function __construct(\Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageFactory) {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => self::NO, 'label' => __('Choose an option')]];
        $pages = $this->pageFactory->create();
        foreach ($pages as $page) {
            $disabled = $page->isActive() ? '' : _('(Disabled)');
            $options[] = ['value' => $page->getId(), 'label' => $page->getTitle() . $disabled];
        }

        return $options;
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
}
