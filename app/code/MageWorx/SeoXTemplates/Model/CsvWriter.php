<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

abstract class CsvWriter implements CsvWriterInterface
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @param Filesystem $filesystem
     * @param MetadataProvider $metadataProvider
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }
}
