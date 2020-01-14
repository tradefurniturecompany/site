<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\Source;

use MageWorx\SeoAll\Model\Source;
use MageWorx\XmlSitemap\Model\GeneratorFactory;

class EntityType extends Source
{
    const DEFAULT_TYPE = 'default';

    const GENERATORS_BY_OBSERVER_TYPE = 'generators_by_observer';

    const ADDITIONAL_LINK_TYPE = 'additional_link';

    /**
     * @var GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * EntityType constructor.
     * @param GeneratorFactory $generatorFactory
     */
    public function __construct(GeneratorFactory $generatorFactory)
    {
        $this->generatorFactory = $generatorFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $generatorArray = [];

        foreach ($this->generatorFactory->getAllGenerators() as $generatorCode => $model) {

            if (!$model->getCode()) {
                continue;
            }

            $generatorArray[] = [
                'value' => $model->getCode(),
                'label' => $model->getName() ?  $model->getName() : $model->getCode()
            ];
        }

        return $generatorArray;
    }
}
