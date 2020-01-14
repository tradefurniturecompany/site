<?php

if (PHP_SAPI == 'cli')
    {
        \Hotlink\Framework\Console\PlatformLocator::register( 'Hotlink\Framework\Model\Platform' );
        \Magento\Framework\Console\CommandLocator::register( 'Hotlink\Framework\Console\CommandList' );
    }
