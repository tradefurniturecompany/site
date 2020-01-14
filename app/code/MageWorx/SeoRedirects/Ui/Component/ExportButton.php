<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Ui\Component;


/**
 * Class ExportButton
 */
class ExportButton extends \Magento\Ui\Component\ExportButton
{
    /**
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $config = $config = $this->getData('config');

        if (isset($config['options'])) {
            $options = [];

            foreach ($config['options'] as $option) {

                // Only CSV format is possible
                if ($option['value'] !== 'csv') {
                    continue;
                }

                $options[] = $option;
            }
            $config['options'] = $options;
            $this->setData('config', $config);
        }
    }
}
