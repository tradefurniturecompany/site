<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class UrlKey
 */
class UrlKey implements OptionSourceInterface
{
    const NO = 0;

    const LINK = 1;

    const CMS_PAGE = 2;

    const LANDING_PAGE = 3;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NO, 'label' => __('Choose an option')],
            ['value' => self::LINK, 'label' => __('Custom URL')],
            ['value' => self::CMS_PAGE, 'label' => __('CMS Page')],
            $this->getLandingOption()
        ];
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
    private function getLandingOption()
    {
        $result = [
            'value' => self::LANDING_PAGE
        ];
        $landingLabel = __('Amasty Landing Page');
        if (!$this->moduleManager->isEnabled('Amasty_Xlanding')) {
            $landingLabel .= sprintf(' (%s)',  __('Not installed'));
            $result['disabled'] = true;
        }
        $result['label'] = $landingLabel;

        return $result;
    }

    /**
     * @return array
     */
    public function getTablesToJoin()
    {
        return [
            self::CMS_PAGE => 'cms_page',
            self::LANDING_PAGE => 'amasty_xlanding_page'
        ];
    }
}
