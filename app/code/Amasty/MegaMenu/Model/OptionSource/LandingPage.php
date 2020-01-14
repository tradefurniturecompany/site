<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LandingPage
 */
class LandingPage implements OptionSourceInterface
{
    const NOT_SELECT = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => self::NOT_SELECT, 'label' => __('Choose an option')]];
        /** @var \Amasty\Xlanding\Model\Page $page */
        foreach ($this->getLandingPages() as $page) {
            $disabled = $page->isActive() ? '' : sprintf(' (%s)',  __('Disabled'));
            $options[] = ['value' => $page->getId(), 'label' => $page->getTitle() . $disabled];
        }

        return $options;
    }

    /**
     * @return array|\Amasty\Xlanding\Model\ResourceModel\Page\Collection
     */
    public function getLandingPages()
    {
        return [];
    }
}
