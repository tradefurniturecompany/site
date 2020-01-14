<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class CarriersOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array|null
     */
    protected $options;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CarriersOptionProvider constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {

            $carriers = [];
            foreach ($this->scopeConfig->getValue('carriers') as $code => $config) {
                if (!empty($config['title'])) {
                    $carriers[] = [
                        'value' => $code, 'label' => $config['title'] . ' [' . $code . ']'
                    ];
                }
            }

            $this->options = $carriers;
        }

        return $this->options;
    }
}
