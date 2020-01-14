<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

class PaymentMethodOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\Config\Initial
     */
    private $initialConfig;

    /**
     * PaymentMethodOptionProvider constructor.
     * @param \Magento\Framework\App\Config\Initial $initialConfig
     */
    public function __construct(\Magento\Framework\App\Config\Initial $initialConfig) {
        $this->initialConfig = $initialConfig;
    }

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $hash = [];
            foreach ($this->initialConfig->getData('default')['payment'] as $code => $config) {
                if (!empty($config['title'])) {
                    $label = '';

                    if (!empty($config['group'])) {
                        $label = ucfirst($config['group']) . ' - ';
                    }

                    $label .= $config['title'];
                    if (!empty($config['allowspecific']) && !empty($config['specificcountry'])) {
                        $label .= ' in ' . $config['specificcountry'];
                    }

                    $hash[$code] = __($label);
                }
            }
            asort($hash);

            $methods = [];
            foreach ($hash as $code => $label) {
                $methods[] = [
                    'value' => $code,
                    'label' => $label
                ];
            }

            $this->options = $methods;
        }

        return $this->options;
    }
}
