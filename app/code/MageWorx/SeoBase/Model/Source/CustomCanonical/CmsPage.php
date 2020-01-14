<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\Source\CustomCanonical;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;

class CmsPage extends \MageWorx\SeoAll\Model\Source
{
    /**
     * @var null|array
     */
    private $options = null;

    /**
     * @var CmsPageCollectionFactory
     */
    private $cmsPageCollectionFactory;

    /**
     * CmsPage constructor.
     *
     * @param CmsPageCollectionFactory $cmsPageCollectionFactory
     */
    public function __construct(
        CmsPageCollectionFactory $cmsPageCollectionFactory
    ) {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $cmsPageCollection = $this->cmsPageCollectionFactory->create();
            $this->options     = [];

            foreach ($cmsPageCollection as $cmsPage) {
                $pageId = $cmsPage->getData('page_id');

                $this->options[] = [
                    'value' => $pageId,
                    'label' => $cmsPage->getData('title') . ' (ID#' . $pageId . ')'
                ];
            }
        }

        return $this->options;
    }
}
